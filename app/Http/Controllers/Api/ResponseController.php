<?php

namespace App\Http\Controllers\Api;

use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Http\Resources\ResponseCollection;
use App\Http\Requests\ResponseStoreRequest;
use App\Http\Requests\ResponseUpdateRequest;

class ResponseController extends Controller
{
    public function index(Request $request): ResponseCollection
    {
        $this->authorize('view-any', Response::class);

        $search = $request->get('search', '');

        $responses = Response::search($search)
            ->latest()
            ->paginate();

        return new ResponseCollection($responses);
    }

    public function store(ResponseStoreRequest $request): ResponseResource
    {
        $this->authorize('create', Response::class);

        $validated = $request->validated();

        $response = Response::create($validated);

        return new ResponseResource($response);
    }

    public function show(Request $request, Response $response): ResponseResource
    {
        $this->authorize('view', $response);

        return new ResponseResource($response);
    }

    public function update(
        ResponseUpdateRequest $request,
        Response $response
    ): ResponseResource {
        $this->authorize('update', $response);

        $validated = $request->validated();

        $response->update($validated);

        return new ResponseResource($response);
    }

    public function destroy(Request $request, Response $response): Response
    {
        $this->authorize('delete', $response);

        $response->delete();

        return response()->noContent();
    }
}
