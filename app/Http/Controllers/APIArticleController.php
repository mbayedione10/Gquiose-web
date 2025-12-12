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
use App\Models\Thematique;
use App\Models\Ville;
use App\Services\ArticleService;
use App\Services\RubriqueService;
use Illuminate\Http\Request;
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

    public function rubriquesWithArticles(RubriqueService $rubriqueService)
    {
        $rubriques = $rubriqueService->allWithArticles();

        return response::success(['rubriques' => $rubriques]);
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
            ->select('id', 'image', "rendez_vous", "structure_url", 'splash')
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

    /**
     * GET /api/v1/informations
     * Récupère les informations de configuration générale (images, URLs)
     */
    public function informations()
    {
        $informations = Information::where('status', true)
            ->select('id', 'image', 'rendez_vous', 'structure_url', 'splash')
            ->first();

        return response::success([
            'informations' => $informations
        ]);
    }

    /**
     * GET /api/v1/quiz
     * Récupère les questions quiz avec filtre optionnel par thématique
     *
     * @param Request $request
     * Query params:
     * - thematique_id (optional): Filtrer par ID de thématique
     * - thematique (optional): Filtrer par nom de thématique
     * - limit (optional): Nombre de questions (défaut: toutes)
     * - random (optional): Mélanger les questions (true/false)
     */
    public function quiz(Request $request)
    {
        $query = DB::table('questions')
            ->join('thematiques', 'questions.thematique_id', 'thematiques.id')
            ->select(
                'questions.id',
                'questions.name as question',
                'questions.reponse',
                'questions.option1',
                'questions.option2',
                'thematiques.id as thematique_id',
                'thematiques.name as thematique'
            )
            ->where('questions.status', true);

        // Filtre par thematique_id
        if ($request->has('thematique_id')) {
            $query->where('thematiques.id', $request->thematique_id);
        }

        // Filtre par nom de thématique
        if ($request->has('thematique')) {
            $query->where('thematiques.name', 'LIKE', '%' . $request->thematique . '%');
        }

        // Mélanger les questions
        if ($request->boolean('random')) {
            $query->inRandomOrder();
        }

        // Limite
        if ($request->has('limit')) {
            $query->limit((int) $request->limit);
        }

        $quiz = $query->get();

        // Récupérer les thématiques disponibles
        $thematiques = Thematique::where('status', true)
            ->select('id', 'name')
            ->withCount(['questions' => function($q) {
                $q->where('status', true);
            }])
            ->get();

        return response::success([
            'quiz' => $quiz,
            'total' => $quiz->count(),
            'thematiques_disponibles' => $thematiques
        ]);
    }

    /**
     * GET /api/v1/thematiques
     * Récupère toutes les thématiques disponibles
     */
    public function thematiques()
    {
        $thematiques = Thematique::where('status', true)
            ->select('id', 'name')
            ->withCount(['questions' => function($q) {
                $q->where('status', true);
            }])
            ->get();

        return response::success([
            'thematiques' => $thematiques,
            'total' => $thematiques->count()
        ]);
    }

    /**
     * GET /api/v1/conseils
     * Récupère les conseils d'hygiène menstruelle
     *
     * @param Request $request
     * Query params:
     * - limit (optional): Nombre de conseils
     * - random (optional): Mélanger les conseils
     */
    public function conseils(Request $request)
    {
        $query = Conseil::select('id', 'message');

        // Mélanger les conseils
        if ($request->boolean('random')) {
            $query->inRandomOrder();
        }

        // Limite
        if ($request->has('limit')) {
            $query->limit((int) $request->limit);
        }

        $conseils = $query->get();

        return response::success([
            'conseils' => $conseils,
            'total' => $conseils->count()
        ]);
    }

    /**
     * GET /api/v1/structures-sante
     * Récupère les structures de santé/aide avec filtres
     *
     * @param Request $request
     * Query params:
     * - ville_id (optional): Filtrer par ID de ville
     * - ville (optional): Filtrer par nom de ville
     * - offre (optional): Filtrer par type d'offre (ex: "Médicale", "Psychosociale")
     * - latitude & longitude (optional): Trier par proximité
     * - rayon (optional): Rayon en km (défaut: 50km)
     * - limit (optional): Nombre de structures
     */
    public function structuresSante(Request $request)
    {
        $query = Structure::where('structures.status', true)
            ->join('villes', 'structures.ville_id', 'villes.id')
            ->select(
                'structures.id',
                'structures.name',
                'structures.description',
                'structures.latitude',
                'structures.longitude',
                'structures.phone',
                'villes.id as ville_id',
                'villes.name as ville',
                'structures.adresse',
                'structures.offre'
            );

        // Filtre par ville_id
        if ($request->has('ville_id')) {
            $query->where('structures.ville_id', $request->ville_id);
        }

        // Filtre par nom de ville
        if ($request->has('ville')) {
            $query->where('villes.name', 'LIKE', '%' . $request->ville . '%');
        }

        // Filtre par type d'offre
        if ($request->has('offre')) {
            $query->where('structures.offre', 'LIKE', '%' . $request->offre . '%');
        }

        // Tri par proximité si coordonnées fournies
        if ($request->has('latitude') && $request->has('longitude')) {
            $lat = $request->latitude;
            $lng = $request->longitude;
            $rayon = $request->input('rayon', 50); // 50km par défaut

            $query->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(structures.latitude)) * cos(radians(structures.longitude) - radians(?)) + sin(radians(?)) * sin(radians(structures.latitude)))) AS distance',
                [$lat, $lng, $lat]
            )
            ->having('distance', '<', $rayon)
            ->orderBy('distance');
        }

        // Limite
        if ($request->has('limit')) {
            $query->limit((int) $request->limit);
        }

        $structures = $query->get();

        // Récupérer les villes disponibles
        $villes = Ville::select('id', 'name')
            ->whereHas('structures', function($q) {
                $q->where('status', true);
            })
            ->orderBy('name')
            ->get();

        // Récupérer les types d'offres uniques
        $offres = Structure::where('status', true)
            ->whereNotNull('offre')
            ->distinct()
            ->pluck('offre')
            ->flatMap(function($offre) {
                return explode(', ', $offre);
            })
            ->unique()
            ->values();

        return response::success([
            'structures' => $structures,
            'total' => $structures->count(),
            'villes_disponibles' => $villes,
            'offres_disponibles' => $offres
        ]);
    }

    /**
     * GET /api/v1/faqs
     * Récupère les questions fréquentes
     */
    public function faqs()
    {
        $faqs = Faq::where('status', true)
            ->select('id', 'question', 'reponse')
            ->get();

        return response::success([
            'faqs' => $faqs,
            'total' => $faqs->count()
        ]);
    }
}
