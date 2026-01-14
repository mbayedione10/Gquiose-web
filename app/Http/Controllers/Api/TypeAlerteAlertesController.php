<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlerteCollection;
use App\Http\Resources\AlerteResource;
use App\Models\TypeAlerte;
use Illuminate\Http\Request;

class TypeAlerteAlertesController extends Controller
{
    public function index(
        Request $request,
        TypeAlerte $typeAlerte
    ): AlerteCollection {
        $this->authorize('view', $typeAlerte);

        $search = $request->get('search', '');

        $alertes = $typeAlerte
            ->alertes()
            ->search($search)
            ->latest()
            ->paginate();

        return new AlerteCollection($alertes);
    }

    public function store(
        Request $request,
        TypeAlerte $typeAlerte
    ): AlerteResource {
        $this->authorize('create', Alerte::class);

        $validated = $request->validate([
            'ref' => ['required', 'unique:alertes,ref', 'max:255', 'string'],
            'description' => ['required', 'max:255', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'etat' => ['required', 'max:255', 'string'],
            'ville_id' => ['required', 'exists:villes,id'],
        ]);

        $alerte = $typeAlerte->alertes()->create($validated);

        return new AlerteResource($alerte);
    }
}
