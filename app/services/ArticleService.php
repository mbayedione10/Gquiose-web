<?php


namespace App\services;


use App\Exceptions\ResourceNotFoundException;
use App\Models\Article;
use App\Models\Rubrique;
use Illuminate\Support\Facades\DB;

class ArticleService
{
    private $VEDETTE_LIMIT = 3;
    private $ARTICLES_LIMIT = 25;

    public function vedette()
    {
        return DB::table('articles')
            ->join('rubriques', 'articles.rubrique_id', 'rubriques.id')
            ->join('users', 'articles.user_id', 'users.id')
            ->select(
                'articles.id',
                'articles.title',
                'articles.description',
                'articles.slug',
                'articles.image',
                'articles.video_url',
                'articles.vedette',
                'rubriques.name as rubrique',
                'users.name as author',
                'articles.created_at')
            ->where('articles.vedette', true)
            ->where('articles.status', true)
            ->orderBy('articles.id', 'desc')
            ->limit($this->VEDETTE_LIMIT)
            ->get();
    }

    public function recent()
    {
        return DB::table('articles')
            ->join('rubriques', 'articles.rubrique_id', 'rubriques.id')
            ->join('users', 'articles.user_id', 'users.id')
            ->select(
                'articles.id',
                'articles.title',
                'articles.description',
                'articles.slug',
                'articles.image',
                'articles.video_url',
                'articles.vedette',
                'rubriques.name as rubrique',
                'users.name as author',
                'articles.created_at')
            ->where('articles.status', true)
            ->orderByDesc('articles.id')
            ->get();
    }
    public function show($slug)
    {
        return DB::table('articles')
            ->join('rubriques', 'articles.rubrique_id', 'rubriques.id')
            ->join('users', 'articles.user_id', 'users.id')
            ->select(
                'articles.id',
                'articles.title',
                'articles.description',
                'articles.slug',
                'articles.image',
                'articles.video_url',
                'articles.vedette',
                'rubriques.name as rubrique',
                'users.name as author',
                'articles.created_at')
            ->where('articles.vedette', true)
            ->where('articles.status', true)
            ->where('articles.slug', $slug)
            ->first();
    }

    public function findByRubrique($rubriqueId)
    {
        $rubrique = Rubrique::whereId($rubriqueId)->first();

        if ($rubrique == null)
            throw new ResourceNotFoundException("La rubrique avec pour ID " .$rubriqueId. " n'existe pas");

        return DB::table('articles')
            ->join('rubriques', 'articles.rubrique_id', 'rubriques.id')
            ->join('users', 'articles.user_id', 'users.id')
            ->select(
                'articles.id',
                'articles.title',
                'articles.description',
                'articles.slug',
                'articles.image',
                'articles.video_url',
                'articles.vedette',
                'rubriques.name as rubrique',
                'users.name as author',
                'articles.created_at')
            ->where('articles.status', true)
            ->where('rubrique_id', $rubriqueId)
            ->orderBy('articles.id', 'desc')
            ->limit($this->ARTICLES_LIMIT)
            ->get();
    }
}
