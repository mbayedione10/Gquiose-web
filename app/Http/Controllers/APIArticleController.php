<?php

namespace App\Http\Controllers;

use App\Exceptions\ResourceNotFoundException;
use App\Http\Responses\ApiResponse as response;

use App\services\ArticleService;
use App\services\RubriqueService;

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


    public function index(RubriqueService  $rubriqueService)
    {
        $vedettes = $this->articleService->vedette();
        $recents = $this->articleService->recent();
        $rubriques = $rubriqueService->all();

        $data = [
            'recents' => $recents,
            'vedettes' => $vedettes,
            'rubriques' => $rubriques,
        ];

        return response::success($data);
    }

    public function show($slug)
    {
        $article = $this->articleService->show($slug);

        if ($article == null)
            throw  new ResourceNotFoundException("Cet article n'existe pas");

        return response::success($article);
    }
}
