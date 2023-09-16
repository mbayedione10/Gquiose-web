<?php

namespace App\Http\Controllers;

use App\Exceptions\ResourceNotFoundException;
use App\Http\Responses\ApiResponse as response;

use App\services\ArticleService;

class APIArticleController extends Controller
{
    private $articleService;

    /**
     * APIArticleController constructor.
     * @param $article
     */
    public function __construct(ArticleService $article)
    {
        $this->articleService = $article;
    }


    public function index()
    {
        $vedette = $this->articleService->vedette();

        return response::success($vedette);
    }

    public function show($slug)
    {
        $article = $this->articleService->show($slug);

        if ($article == null)
            throw  new ResourceNotFoundException("Cet article n'existe pas");

        return response::success($article);
    }
}
