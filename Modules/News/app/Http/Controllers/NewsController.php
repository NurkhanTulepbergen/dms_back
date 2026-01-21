<?php

namespace Modules\News\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\News\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index() {
        $news = News::orderByDesc('created_at')->get();

        return result($news, 200, 'Новости');
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|string',
        ]);
        $news = News::create([
            'title' => $request->title,
            'description' => $request->description,
            'photo' => $request->photo,
        ]);
        return result($news, 201, 'Новость успешна создана');
    }

    public function show($id) {
        $news = News::findOrfail($id);

        return result($news, 200, 'Новость');
    }

    public function update(Request $request, $id) {
        $news=News::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|string',
        ]);

        $news->update($request->only([
            'title',
            'description',
            'photo',
        ]));
        return result($news, 200, 'Новость обновлена');
    }

    public function destroy($id) {
        $news = News::findOrFail($id);
        $news->delete();

        return response()->noContent();
    }
}
