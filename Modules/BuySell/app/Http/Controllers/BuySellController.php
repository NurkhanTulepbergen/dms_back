<?php

namespace Modules\BuySell\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\BuySell\Models\BuySellListing;
use Modules\BuySell\Services\BuySellService;
use Modules\User\Models\User;

class BuySellController extends Controller
{
    public function __construct(
        private readonly BuySellService $buySellService,
    ) {}

    public function meta()
    {
        return result([
            'categories' => $this->buySellService->categories(),
            'conditions' => $this->buySellService->conditions(),
            'statuses' => $this->buySellService->statuses(),
        ], 200, 'Buy-sell meta');
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $query = BuySellListing::query()
            ->with('user')
            ->latest('id');

        $mine = $request->boolean('mine');
        if ($mine) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('status', 'active');
        }

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('pickup_location', 'like', "%{$search}%");
            });
        }

        $category = (string) $request->input('category', '');
        if (in_array($category, $this->buySellService->categoryValues(), true)) {
            $query->where('category', $category);
        }

        $excludeId = (int) $request->input('exclude_id', 0);
        if ($excludeId > 0) {
            $query->whereKeyNot($excludeId);
        }

        $limit = min(max((int) $request->input('limit', 24), 1), 100);

        $listings = $query
            ->limit($limit)
            ->get()
            ->map(fn (BuySellListing $listing) => $this->buySellService->serializeListing($listing, $user))
            ->values();

        return result($listings, 200, 'Buy-sell listings');
    }

    public function mine(Request $request)
    {
        $request->merge(['mine' => true]);

        return $this->index($request);
    }

    public function show(Request $request, BuySellListing $listing)
    {
        /** @var User $user */
        $user = $request->user();
        $listing->load('user');

        if (! $this->buySellService->canViewListing($user, $listing)) {
            return result(null, 404, 'Объявление не найдено');
        }

        return result(
            $this->buySellService->serializeListing($listing, $user),
            200,
            'Buy-sell listing'
        );
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $this->validatePayload($request, true);
        $listing = $this->buySellService->createListing(
            $validated,
            $user,
            $request->file('images', []),
        );

        return result(
            $this->buySellService->serializeListing($listing, $user),
            201,
            'Объявление создано'
        );
    }

    public function update(Request $request, BuySellListing $listing)
    {
        /** @var User $user */
        $user = $request->user();
        $listing->load('user');

        if (! $this->buySellService->canManageListing($user, $listing)) {
            return result(null, 403, 'Нет доступа к изменению объявления');
        }

        $validated = $this->validatePayload($request, false, $listing);
        $listing = $this->buySellService->updateListing(
            $listing,
            $validated,
            $request->file('images', []),
        );

        return result(
            $this->buySellService->serializeListing($listing, $user),
            200,
            'Объявление обновлено'
        );
    }

    public function destroy(Request $request, BuySellListing $listing)
    {
        /** @var User $user */
        $user = $request->user();
        $listing->load('user');

        if (! $this->buySellService->canManageListing($user, $listing)) {
            return result(null, 403, 'Нет доступа к удалению объявления');
        }

        $this->buySellService->deleteListing($listing);

        return response()->noContent();
    }

    private function validatePayload(
        Request $request,
        bool $isCreate,
        ?BuySellListing $listing = null
    ): array {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:160'],
            'category' => ['required', Rule::in($this->buySellService->categoryValues())],
            'condition' => ['required', Rule::in($this->buySellService->conditionValues())],
            'price' => ['required', 'numeric', 'min:0'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in($this->buySellService->statusValues())],
            'description' => ['required', 'string', 'max:5000'],
            'existing_images' => [$isCreate ? 'nullable' : 'required', 'array', 'max:5'],
            'existing_images.*' => ['string'],
            'images' => [$isCreate ? 'required' : 'nullable', 'array', 'max:5'],
            'images.*' => ['image', 'max:5120'],
        ], [
            'images.required' => 'Добавьте хотя бы одно изображение',
        ]);

        $validated = $validator->validate();

        $existingImages = $validated['existing_images'] ?? [];
        if (! $isCreate && $listing) {
            $existingImages = array_values(array_intersect(
                $existingImages,
                $listing->image_paths ?? [],
            ));
        }

        $newImageCount = count($request->file('images', []));
        $totalImages = count($existingImages) + $newImageCount;

        if ($totalImages < 1 || $totalImages > 5) {
            throw ValidationException::withMessages([
                'images' => ['У объявления должно быть от 1 до 5 изображений'],
            ]);
        }

        $validated['existing_images'] = $existingImages;

        return $validated;
    }
}
