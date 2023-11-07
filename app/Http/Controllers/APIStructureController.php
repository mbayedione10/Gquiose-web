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
                'villes.name as ville',
                'structures.adresse',
            )
            ->where('structures.status', true)
            ->get();

        $data = ["structures" => $structures];

        return response::success($data);
    }
}
