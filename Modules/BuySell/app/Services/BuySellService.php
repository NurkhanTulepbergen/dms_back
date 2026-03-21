<?php

namespace Modules\BuySell\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\BuySell\Models\BuySellListing;
use Modules\User\Models\User;

class BuySellService
{
    private const CATEGORY_LABELS = [
        'textbooks' => 'Учебники',
        'electronics' => 'Техника',
        'home' => 'Быт',
        'clothing' => 'Одежда',
        'other' => 'Другое',
    ];

    private const CONDITION_LABELS = [
        'new' => 'Новое',
        'like_new' => 'Почти новое',
        'good' => 'Хорошее состояние',
        'fair' => 'Есть следы использования',
    ];

    private const STATUS_LABELS = [
        'draft' => 'Черновик',
        'active' => 'Активно',
        'sold' => 'Продано',
    ];

    public function categories(): array
    {
        return $this->mapOptions(self::CATEGORY_LABELS);
    }

    public function conditions(): array
    {
        return $this->mapOptions(self::CONDITION_LABELS);
    }

    public function statuses(): array
    {
        return $this->mapOptions(self::STATUS_LABELS);
    }

    public function categoryValues(): array
    {
        return array_keys(self::CATEGORY_LABELS);
    }

    public function conditionValues(): array
    {
        return array_keys(self::CONDITION_LABELS);
    }

    public function statusValues(): array
    {
        return array_keys(self::STATUS_LABELS);
    }

    public function canManageListing(User $user, BuySellListing $listing): bool
    {
        return $user->id === $listing->user_id || $this->isPrivileged($user);
    }

    public function canViewListing(User $user, BuySellListing $listing): bool
    {
        if ($listing->status === 'active') {
            return true;
        }

        return $this->canManageListing($user, $listing);
    }

    public function createListing(array $payload, User $user, array $uploadedImages): BuySellListing
    {
        $storedImages = $this->storeUploadedImages($uploadedImages);

        $listing = BuySellListing::query()->create([
            'user_id' => $user->id,
            'title' => $payload['title'],
            'category' => $payload['category'],
            'condition' => $payload['condition'],
            'price' => $payload['price'],
            'pickup_location' => $payload['pickup_location'] ?? null,
            'contact_phone' => $payload['contact_phone'] ?? $user->phone_number ?? null,
            'status' => $payload['status'],
            'description' => $payload['description'],
            'image_paths' => $storedImages,
            'published_at' => $payload['status'] === 'active' ? now() : null,
            'sold_at' => $payload['status'] === 'sold' ? now() : null,
        ]);

        return $listing->load('user');
    }

    public function updateListing(
        BuySellListing $listing,
        array $payload,
        array $uploadedImages
    ): BuySellListing {
        $existingImages = array_values(array_intersect(
            $payload['existing_images'] ?? [],
            $listing->image_paths ?? [],
        ));

        $newImages = $this->storeUploadedImages($uploadedImages);
        $finalImages = array_values(array_merge($existingImages, $newImages));

        $removedImages = array_values(array_diff($listing->image_paths ?? [], $existingImages));
        $this->deleteImages($removedImages);

        $publishedAt = $listing->published_at;
        if (($payload['status'] ?? $listing->status) === 'active' && ! $publishedAt) {
            $publishedAt = now();
        }

        $soldAt = ($payload['status'] ?? $listing->status) === 'sold'
            ? ($listing->sold_at ?? now())
            : null;

        $listing->update([
            'title' => $payload['title'],
            'category' => $payload['category'],
            'condition' => $payload['condition'],
            'price' => $payload['price'],
            'pickup_location' => $payload['pickup_location'] ?? null,
            'contact_phone' => $payload['contact_phone'] ?? $listing->user?->phone_number ?? null,
            'status' => $payload['status'],
            'description' => $payload['description'],
            'image_paths' => $finalImages,
            'published_at' => $publishedAt,
            'sold_at' => $soldAt,
        ]);

        return $listing->fresh(['user']);
    }

    public function deleteListing(BuySellListing $listing): void
    {
        $this->deleteImages($listing->image_paths ?? []);
        $listing->delete();
    }

    public function serializeListing(BuySellListing $listing, ?User $viewer = null): array
    {
        $seller = $listing->user;
        $images = array_values(array_filter(array_map(
            fn (string $path) => $this->toPublicImageUrl($path),
            $listing->image_paths ?? [],
        )));

        return [
            'id' => $listing->id,
            'title' => $listing->title,
            'price' => (float) $listing->price,
            'category' => $listing->category,
            'category_label' => self::CATEGORY_LABELS[$listing->category] ?? $listing->category,
            'condition' => $listing->condition,
            'condition_label' => self::CONDITION_LABELS[$listing->condition] ?? $listing->condition,
            'status' => $listing->status,
            'status_label' => self::STATUS_LABELS[$listing->status] ?? $listing->status,
            'description' => $listing->description,
            'pickup_location' => $listing->pickup_location,
            'contact_phone' => $listing->contact_phone ?: $seller?->phone_number,
            'images' => $images,
            'image_paths' => $listing->image_paths ?? [],
            'cover_image' => $images[0] ?? null,
            'created_at' => optional($listing->created_at)?->toIso8601String(),
            'published_at' => optional($listing->published_at)?->toIso8601String(),
            'sold_at' => optional($listing->sold_at)?->toIso8601String(),
            'seller' => [
                'id' => $seller?->id,
                'name' => $seller ? trim($seller->name.' '.$seller->lastname) : 'Студент',
                'full_name' => $seller ? $this->formatUserName($seller) : 'Студент',
                'phone_number' => $seller?->phone_number,
                'uni_id' => $seller?->uni_id,
            ],
            'is_owner' => $viewer ? $viewer->id === $listing->user_id : false,
        ];
    }

    private function mapOptions(array $labels): array
    {
        return array_map(
            fn (string $value, string $label) => ['value' => $value, 'label' => $label],
            array_keys($labels),
            array_values($labels),
        );
    }

    /**
     * @param  array<int, UploadedFile>  $uploadedImages
     * @return array<int, string>
     */
    private function storeUploadedImages(array $uploadedImages): array
    {
        $paths = [];

        foreach ($uploadedImages as $image) {
            if (! $image instanceof UploadedFile) {
                continue;
            }

            $paths[] = $image->store('buy-sell', 'public');
        }

        return $paths;
    }

    /**
     * @param  array<int, string>  $paths
     */
    private function deleteImages(array $paths): void
    {
        foreach ($paths as $path) {
            if (! $path || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                continue;
            }

            Storage::disk('public')->delete($path);
        }
    }

    private function toPublicImageUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url(Storage::disk('public')->url($path));
    }

    private function formatUserName(User $user): string
    {
        return trim(implode(' ', array_filter([
            $user->lastname,
            $user->name,
            $user->middlename,
        ])));
    }

    private function isPrivileged(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager'], true);
    }
}
