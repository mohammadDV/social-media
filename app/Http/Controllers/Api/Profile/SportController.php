<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\SportRequest;
use App\Http\Requests\SportUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Models\Sport;
use App\Repositories\Contracts\ISportRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SportController extends Controller
{
    /**
     * Constructor of SportController.
     */
    public function __construct(protected ISportRepository $repository)
    {
        //
    }

    /**
     * Get all of sports with pagination
     */
    public function indexPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get the sport.
     */
    public function show(Sport $sport): JsonResponse
    {
        return response()->json($this->repository->show($sport), Response::HTTP_OK);
    }

    /**
     * Store the sport.
     */
    public function store(SportRequest $request): JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Update the sport.
     */
    public function update(SportUpdateRequest $request, Sport $sport): JsonResponse
    {
        return $this->repository->update($request, $sport);
    }

    /**
     * Delete the sport.
     */
    public function destroy(Sport $sport): JsonResponse
    {
        return $this->repository->destroy($sport);
    }
}
