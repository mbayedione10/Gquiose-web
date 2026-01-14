<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleStoreRequest;
use App\Http\Requests\ArticleUpdateRequest;
use App\Models\Article;
use App\Models\Rubrique;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', Article::class);

        $search = $request->get('search', '');

        $articles = Article::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.articles.index', compact('articles', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Article::class);

        $rubriques = Rubrique::pluck('name', 'id');
        $users = User::pluck('name', 'id');

        return view('app.articles.create', compact('rubriques', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ArticleStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Article::class);

        $validated = $request->validated();
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('public');
        }

        $article = Article::create($validated);

        return redirect()
            ->route('articles.edit', $article)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Article $article): View
    {
        $this->authorize('view', $article);

        return view('app.articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Article $article): View
    {
        $this->authorize('update', $article);

        $rubriques = Rubrique::pluck('name', 'id');
        $users = User::pluck('name', 'id');

        return view(
            'app.articles.edit',
            compact('article', 'rubriques', 'users')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ArticleUpdateRequest $request,
        Article $article
    ): RedirectResponse {
        $this->authorize('update', $article);

        $validated = $request->validated();
        if ($request->hasFile('image')) {
            if ($article->image) {
                Storage::delete($article->image);
            }

            $validated['image'] = $request->file('image')->store('public');
        }

        $article->update($validated);

        return redirect()
            ->route('articles.edit', $article)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Article $article
    ): RedirectResponse {
        $this->authorize('delete', $article);

        if ($article->image) {
            Storage::delete($article->image);
        }

        $article->delete();

        return redirect()
            ->route('articles.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
