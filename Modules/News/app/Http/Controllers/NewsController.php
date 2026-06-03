<?php

namespace Modules\News\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\News\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index() {
        $news = News::orderByDesc('created_at')
            ->get()
            ->map(fn (News $item) => $this->toResponse($item));

        return result($news, 200, 'Новости');
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'translations' => 'required|array',
            'translations.kk.title' => 'required|string|max:255',
            'translations.kk.description' => 'required|string',
            'translations.en.title' => 'required|string|max:255',
            'translations.en.description' => 'required|string',
            'photo' => 'nullable|string',
        ]);

        $title = trim($request->title);
        $description = trim($request->description);

        $news = News::create([
            'title' => $title,
            'description' => $description,
            'translations' => $this->translationsFromRequest($request, $title, $description),
            'photo' => $request->photo,
        ]);

        return result($this->toResponse($news), 201, 'Новость успешна создана');
    }

    public function show($id) {
        $news = News::findOrfail($id);

        return result($this->toResponse($news), 200, 'Новость');
    }

    public function update(Request $request, $id) {
        $news=News::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'translations' => 'required|array',
            'translations.kk.title' => 'required|string|max:255',
            'translations.kk.description' => 'required|string',
            'translations.en.title' => 'required|string|max:255',
            'translations.en.description' => 'required|string',
            'photo' => 'nullable|string',
        ]);

        $title = trim($request->title);
        $description = trim($request->description);

        $news->update([
            'title' => $title,
            'description' => $description,
            'translations' => $this->translationsFromRequest($request, $title, $description),
            'photo' => $request->photo,
        ]);

        return result($this->toResponse($news), 200, 'Новость обновлена');
    }

    public function destroy($id) {
        $news = News::findOrFail($id);
        $news->delete();

        return response()->noContent();
    }

    private function toResponse(News $news): array
    {
        return [
            'id' => $news->id,
            'title' => $news->localizedTitle(),
            'description' => $news->localizedDescription(),
            'title_ru' => $news->title,
            'description_ru' => $news->description,
            'translations' => $news->translations ?: [
                'ru' => [
                    'title' => $news->title,
                    'description' => $news->description,
                ],
            ],
            'photo' => $news->photo,
            'created_at' => $news->created_at,
            'updated_at' => $news->updated_at,
        ];
    }

    private function translationsFromRequest(Request $request, string $title, string $description): array
    {
        $translations = $request->input('translations', []);

        return [
            'ru' => [
                'title' => $title,
                'description' => $description,
            ],
            'kk' => [
                'title' => trim((string) data_get($translations, 'kk.title')),
                'description' => trim((string) data_get($translations, 'kk.description')),
            ],
            'en' => [
                'title' => trim((string) data_get($translations, 'en.title')),
                'description' => trim((string) data_get($translations, 'en.description')),
            ],
        ];
    }
}
