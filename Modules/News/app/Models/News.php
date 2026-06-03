<?php

namespace Modules\News\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title',
        'description',
        'translations',
        'photo',
    ];

    protected $casts = [
        'translations' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (News $news) {
            $translations = is_array($news->translations) ? $news->translations : [];

            $translations['ru'] = [
                'title' => (string) $news->title,
                'description' => (string) $news->description,
            ];

            foreach (['kk', 'en'] as $locale) {
                $translations[$locale] = [
                    'title' => (string) ($translations[$locale]['title'] ?? $news->title),
                    'description' => (string) ($translations[$locale]['description'] ?? $news->description),
                ];
            }

            $news->translations = $translations;
        });
    }

    public function localizedTitle(?string $locale = null): string
    {
        return $this->localizedValue('title', $locale) ?? $this->title;
    }

    public function localizedDescription(?string $locale = null): string
    {
        return $this->localizedValue('description', $locale) ?? $this->description;
    }

    private function localizedValue(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        $translations = $this->translations ?: [];
        $value = $translations[$locale][$field] ?? null;

        if (is_string($value) && trim($value) !== '') {
            return $value;
        }

        $fallback = $translations['ru'][$field] ?? null;

        return is_string($fallback) && trim($fallback) !== '' ? $fallback : null;
    }
}
