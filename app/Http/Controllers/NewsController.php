<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index() {
        return response()->json(
            News::orderByDesc('created_at')->get(), 200
        );
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
        response()->json($news, 201);
    }

    public function show($id) {
        $news = News::find($id);

        if(!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }

        return response()->json($news, 200);
    }

    public function update(Request $request, $id) {
        $news=News::find($id);
        if(!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }

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
        return response()->json($news, 200);
    }

    public function destroy($id) {
        $news = News::find($id);

        if(!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }

        $news->delete();

        return response()->json(['message' => 'News successfully deleted'], 200);
    }
}
