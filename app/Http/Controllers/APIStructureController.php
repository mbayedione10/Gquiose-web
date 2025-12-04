<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse as response;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class APIStructureController extends Controller
{
    public function list()
    {
        $structures = DB::table('structures')
            ->join('type_structures', 'structures.type_structure_id', 'type_structures.id')
            ->join('villes', 'structures.ville_id', 'villes.id')
            ->select(
                'structures.id',
                'structures.name',
                'structures.description',
                'structures.latitude',
                'structures.longitude',
                'structures.phone',
                'type_structures.name as type',
                'type_structures.icon as icon',
                'villes.name as ville',
                'structures.adresse',
            )
            ->where('structures.status', true)
            ->get();

        $data = ["structures" => $structures];

        return response::success($data);
    }

    /**
     * Récupérer les structures à proximité d'une position géographique
     * Utilise la formule de Haversine pour calculer la distance
     */
    public function nearby(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:200', // Rayon en km (max 200km)
            'type_structure_id' => 'nullable|exists:type_structures,id',
        ]);

        $lat = $validated['lat'];
        $lng = $validated['lng'];
        $radius = $validated['radius'] ?? 50; // Défaut: 50km
        $typeStructureId = $validated['type_structure_id'] ?? null;

        // Formule de Haversine pour calculer la distance en km
        // 6371 = rayon de la Terre en km
        $haversine = "(6371 * acos(cos(radians($lat)) 
                     * cos(radians(structures.latitude)) 
                     * cos(radians(structures.longitude) - radians($lng)) 
                     + sin(radians($lat)) 
                     * sin(radians(structures.latitude))))";

        $query = DB::table('structures')
            ->join('type_structures', 'structures.type_structure_id', 'type_structures.id')
            ->join('villes', 'structures.ville_id', 'villes.id')
            ->select(
                'structures.id',
                'structures.name',
                'structures.description',
                'structures.latitude',
                'structures.longitude',
                'structures.phone',
                'structures.adresse',
                'structures.offre',
                'type_structures.name as type',
                'type_structures.icon as icon',
                'villes.name as ville',
                DB::raw("$haversine AS distance")
            )
            ->where('structures.status', true);

        // Filtrer par type de structure si spécifié
        if ($typeStructureId) {
            $query->where('structures.type_structure_id', $typeStructureId);
        }

        $structures = $query->get();

        // Filtrer par distance et formater avec 2 décimales (après récupération pour compatibilité SQLite)
        $structures = $structures->filter(function ($structure) use ($radius) {
            return $structure->distance <= $radius;
        })
        ->map(function ($structure) {
            $structure->distance = round($structure->distance, 2);
            return $structure;
        })
        ->sortBy('distance')
        ->values(); // Réindexer après tri

        $data = [
            "structures" => $structures,
            "search_params" => [
                "latitude" => $lat,
                "longitude" => $lng,
                "radius_km" => $radius,
                "type_structure_id" => $typeStructureId,
            ],
            "count" => $structures->count(),
        ];

        return response::success($data);
    }
}
