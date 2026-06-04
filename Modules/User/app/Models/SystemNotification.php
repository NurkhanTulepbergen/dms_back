<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemNotification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'translations',
        'action_url',
        'created_by',
    ];

    protected $casts = [
        'translations' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (SystemNotification $notification) {
            $translations = is_array($notification->translations) ? $notification->translations : [];

            $translations['ru'] = [
                'title' => (string) $notification->title,
                'message' => (string) $notification->message,
            ];

            foreach (['kk', 'en'] as $locale) {
                $localized = is_array($translations[$locale] ?? null) ? $translations[$locale] : [];

                $translations[$locale] = [
                    'title' => (string) ($localized['title'] ?? $notification->title),
                    'message' => (string) ($localized['message'] ?? $notification->message),
                ];
            }

            $notification->translations = $translations;
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function localizedTitle(?string $locale = null): string
    {
        return $this->localizedValue('title', $locale) ?? $this->title;
    }

    public function localizedMessage(?string $locale = null): string
    {
        return $this->localizedValue('message', $locale) ?? $this->message;
    }

    private function localizedValue(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        $translations = $this->translations ?: [];
        $localized = is_array($translations[$locale] ?? null) ? $translations[$locale] : [];
        $value = $localized[$field] ?? null;

        if (is_string($value) && trim($value) !== '') {
            return $value;
        }

        $fallbackTranslation = is_array($translations['ru'] ?? null) ? $translations['ru'] : [];
        $fallback = $fallbackTranslation[$field] ?? null;

        return is_string($fallback) && trim($fallback) !== '' ? $fallback : null;
    }
}
