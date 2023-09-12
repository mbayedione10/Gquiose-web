<?php

namespace App\Http\Controllers\Api;

use App\Models\Rubrique;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleCollection;

class RubriqueArticlesController extends Controller
{
    public function index(
        Request $request,
        Rubrique $rubrique
    ): ArticleCollection {
        $this->authorize('view', $rubrique);

        $search = $request->get('search', '');

        $articles = $rubrique
            ->articles()
            ->search($search)
            ->latest()
            ->paginate();

        return new ArticleCollection($articles);
    }

    public function store(Request $request, Rubrique $rubrique): ArticleResource
    {
        $this->authorize('create', Article::class);

        $validated = $request->validate([
            'title' => ['required', 'max:255', 'string'],
            'description' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'unique:articles,slug', 'max:255', 'string'],
            'image' => ['nullable', 'image', 'max:1024'],
            'status' => ['required', 'boolean'],
            'user_id' => ['required', 'exists:users,id'],
            'video_url' => ['nullable', 'max:255', 'string'],
            'audio_url' => ['nullable', 'max:255', 'string'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('public');
        }

        $article = $rubrique->articles()->create($validated);

        return new ArticleResource($article);
    }
}
