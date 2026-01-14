<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SuiviCollection;
use App\Http\Resources\SuiviResource;
use App\Models\Alerte;
use Illuminate\Http\Request;

class AlerteSuivisController extends Controller
{
    public function index(Request $request, Alerte $alerte): SuiviCollection
    {
        $this->authorize('view', $alerte);

        $search = $request->get('search', '');

        $suivis = $alerte
            ->suivis()
            ->search($search)
            ->latest()
            ->paginate();

        return new SuiviCollection($suivis);
    }

    public function store(Request $request, Alerte $alerte): SuiviResource
    {
        $this->authorize('create', Suivi::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'observation' => ['required', 'max:255', 'string'],
        ]);

        $suivi = $alerte->suivis()->create($validated);

        return new SuiviResource($suivi);
    }
}
