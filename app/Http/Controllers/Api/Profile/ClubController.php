<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClubRequest;
use App\Http\Requests\ClubUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Models\Club;
use App\Models\Country;
use App\Models\Sport;
use App\Repositories\Contracts\IClubRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ClubController extends Controller
{
    /**
     * Constructor of ClubController.
     */
    public function __construct(protected IClubRepository $repository)
    {
        //
    }

    /**
     * Get all of clubs with pagination
     */
    public function index(?Sport $sport, ?Country $country): JsonResponse
    {
        return response()->json($this->repository->index($sport, $country), Response::HTTP_OK);
    }

    /**
     * Get all of clubs with pagination
     */
    public function indexPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get the club.
     */
    public function show(Club $club): JsonResponse
    {
        return response()->json($this->repository->show($club), Response::HTTP_OK);
    }

    /**
     * Store the club.
     */
    public function store(ClubRequest $request): JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Update the club.
     */
    public function update(ClubUpdateRequest $request, Club $club): JsonResponse
    {
        return $this->repository->update($request, $club);
    }

    /**
     * Does the user follow the club or not.
     */
    public function isActive(Club $club): JsonResponse
    {
        return response()->json($this->repository->isActive($club), Response::HTTP_OK);
    }

    /**
     * Delete the club.
     */
    public function destroy(Club $club): JsonResponse
    {
        return $this->repository->destroy($club);
    }
}
