<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StructureCollection;
use App\Http\Resources\StructureResource;
use App\Models\TypeStructure;
use Illuminate\Http\Request;

class TypeStructureStructuresController extends Controller
{
    public function index(
        Request $request,
        TypeStructure $typeStructure
    ): StructureCollection {
        $this->authorize('view', $typeStructure);

        $search = $request->get('search', '');

        $structures = $typeStructure
            ->structures()
            ->search($search)
            ->latest()
            ->paginate();

        return new StructureCollection($structures);
    }

    public function store(
        Request $request,
        TypeStructure $typeStructure
    ): StructureResource {
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
            'status' => ['required', 'boolean'],
            'ville_id' => ['required', 'exists:villes,id'],
            'adresse' => ['required', 'max:255', 'string'],
        ]);

        $structure = $typeStructure->structures()->create($validated);

        return new StructureResource($structure);
    }
}
