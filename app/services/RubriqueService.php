<?php


namespace App\services;


use App\Models\Rubrique;
use Illuminate\Support\Facades\DB;

class RubriqueService
{
    public function all()
    {
        return Rubrique::has('articles')
            ->select('id', 'name')
            ->get();
    }

    public function allWithArticles()
    {
        return Rubrique::with(['articles' => function ($query) {
            $query->where('status', true)
                ->select('id', 'title', 'description', 'slug', 'image', 'video_url', 'vedette', 'rubrique_id', 'created_at')
                ->orderByDesc('id');
        }])
        ->has('articles')
        ->select('id', 'name')
        ->get()
        ->map(function ($rubrique) {
            return [
                'id' => $rubrique->id,
                'name' => $rubrique->name,
                'articles_count' => $rubrique->articles->count(),
                'articles' => $rubrique->articles->map(function ($article) {
                    return [
                        'id' => $article->id,
                        'title' => $article->title,
                        'description' => $article->description,
                        'slug' => $article->slug,
                        'image' => $article->image,
                        'video_url' => $article->video_url,
                        'vedette' => $article->vedette,
                        'created_at' => $article->created_at,
                    ];
                }),
            ];
        });
    }

}
