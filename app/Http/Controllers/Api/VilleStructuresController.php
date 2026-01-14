<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StructureCollection;
use App\Http\Resources\StructureResource;
use App\Models\Ville;
use Illuminate\Http\Request;

class VilleStructuresController extends Controller
{
    public function index(Request $request, Ville $ville): StructureCollection
    {
        $this->authorize('view', $ville);

        $search = $request->get('search', '');

        $structures = $ville
            ->structures()
            ->search($search)
            ->latest()
            ->paginate();

        return new StructureCollection($structures);
    }

    public function store(Request $request, Ville $ville): StructureResource
    {
        $this->authorize('create', Structure::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'description' => ['nullable', 'max:255', 'string'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'phone' => [
                'required',
                'unique:structures,phone',
                'max:255',
                'string',
            ],
            'type_structure_id' => ['required', 'exists:type_structures,id'],
            'status' => ['required', 'boolean'],
            'adresse' => ['required', 'max:255', 'string'],
        ]);

        $structure = $ville->structures()->create($validated);

        return new StructureResource($structure);
    }
}
