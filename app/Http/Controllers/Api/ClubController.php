<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Repositories\Contracts\IClubRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ClubController extends Controller
{
    /**
     * Constructor of ILiveRepository.
     */
    public function __construct(protected IClubRepository $repository)
    {
        //
    }

    /**
     * Get all of lives.
     * @param Club $club
     */
    public function getInfo(Club $club): JsonResponse
    {
        return response()->json($this->repository->getInfo($club), Response::HTTP_OK);
    }

}
