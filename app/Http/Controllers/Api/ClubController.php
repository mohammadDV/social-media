<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TableRequest;
use App\Models\Club;
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
     * Get the club info.
     * @param Club $club
     */
    public function getInfo(Club $club): JsonResponse
    {
        return response()->json($this->repository->getInfo($club), Response::HTTP_OK);
    }

    /**
     * Get the club followers.
     * @param TableRequest $request
     * @param Club $club
     */
    public function getFollowers(TableRequest $request, Club $club): JsonResponse
    {
        return response()->json($this->repository->getFollowers($request, $club), Response::HTTP_OK);
    }

}
