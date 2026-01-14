<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlerteCollection;
use App\Http\Resources\AlerteResource;
use App\Models\Ville;
use Illuminate\Http\Request;

class VilleAlertesController extends Controller
{
    public function index(Request $request, Ville $ville): AlerteCollection
    {
        $this->authorize('view', $ville);

        $search = $request->get('search', '');

        $alertes = $ville
            ->alertes()
            ->search($search)
            ->latest()
            ->paginate();

        return new AlerteCollection($alertes);
    }

    public function store(Request $request, Ville $ville): AlerteResource
    {
        $this->authorize('create', Alerte::class);

        $validated = $request->validate([
            'ref' => ['required', 'unique:alertes,ref', 'max:255', 'string'],
            'description' => ['required', 'max:255', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'type_alerte_id' => ['required', 'exists:type_alertes,id'],
            'etat' => ['required', 'max:255', 'string'],
        ]);

        $alerte = $ville->alertes()->create($validated);

        return new AlerteResource($alerte);
    }
}
