<?php

namespace App\Http\Controllers;

use App\Exceptions\ResourceNotFoundException;
use App\Http\Responses\ApiResponse as response;

use App\Models\Censure;
use App\Models\Conseil;
use App\Models\Faq;
use App\Models\Information;
use App\Models\Question;
use App\Models\Rubrique;
use App\Models\Structure;
use App\Models\Theme;
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
            ->select('id', 'image', "rendez_vous", "structure_url")
            ->first();

        $conseils = Conseil::select("id", "message")->get();

        $structures = Structure::where('structures.status', true)
            ->join('villes', 'structures.ville_id', 'villes.id')
            ->select('structures.id', 'structures.name', 'structures.description', 'structures.latitude',
                'structures.longitude', 'structures.phone', 'villes.name as ville', 'structures.adresse', 'structures.offre')
            ->get();

        $faqs = Faq::where('status', true)
            ->select('id', 'question', 'reponse')
            ->get();

        $themes = Theme::where('status', true)
                ->select('id', 'name')
                ->get();

        $censures = Censure::select('id','name')
            ->get();

        $data = [
            'informations' => $informations,
            'quiz' => $quiz,
            'conseils' => $conseils,
            'structures' => $structures,
            'faqs' => $faqs,
            'themes' => $themes,
            'censures' => $censures,
        ];

        return response::success($data);
    }
}
