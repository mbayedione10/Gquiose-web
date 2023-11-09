<?php

namespace App\Http\Controllers;

use App\Exceptions\ResourceNotFoundException;
use App\Http\Responses\ApiResponse as response;

use App\Models\Information;
use App\Models\Question;
use App\Models\Rubrique;
use App\services\ArticleService;
use App\services\RubriqueService;
use Illuminate\Support\Facades\DB;

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

    public function showByRubrique($rubriqueId)
    {

        $articles= $this->articleService->findByRubrique($rubriqueId);

        return response::success($articles);

    }

    public function config()
    {
        $quiz = DB::table('questions')
            ->join('thematiques', 'questions.thematique_id', 'thematiques.id')
            ->select(
                'questions.id',
                'questions.name',
                'questions.reponse',
                'questions.option1',
                'questions.option2',
                'thematiques.id as thematique_id',
                'thematiques.name as thematique'
            )
            ->where('questions.status', true)
            ->get();

        $informations = Information::where('status', true)
            ->select('id', 'image', "rendez_vous")
            ->first();

        $data = [
            'informations' => $informations,
            'quiz' => $quiz
        ];

        return response::success($data);
    }
}
