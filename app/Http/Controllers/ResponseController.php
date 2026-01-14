<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResponseStoreRequest;
use App\Http\Requests\ResponseUpdateRequest;
use App\Models\Question;
use App\Models\Response;
use App\Models\Utilisateur;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', Response::class);

        $search = $request->get('search', '');

        $responses = Response::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.responses.index', compact('responses', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Response::class);

        $questions = Question::pluck('name', 'id');
        $utilisateurs = Utilisateur::pluck('nom', 'id');

        return view(
            'app.responses.create',
            compact('questions', 'utilisateurs')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ResponseStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Response::class);

        $validated = $request->validated();

        $response = Response::create($validated);

        return redirect()
            ->route('responses.edit', $response)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Response $response): View
    {
        $this->authorize('view', $response);

        return view('app.responses.show', compact('response'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Response $response): View
    {
        $this->authorize('update', $response);

        $questions = Question::pluck('name', 'id');
        $utilisateurs = Utilisateur::pluck('nom', 'id');

        return view(
            'app.responses.edit',
            compact('response', 'questions', 'utilisateurs')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ResponseUpdateRequest $request,
        Response $response
    ): RedirectResponse {
        $this->authorize('update', $response);

        $validated = $request->validated();

        $response->update($validated);

        return redirect()
            ->route('responses.edit', $response)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Response $response
    ): RedirectResponse {
        $this->authorize('delete', $response);

        $response->delete();

        return redirect()
            ->route('responses.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
