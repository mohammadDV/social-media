<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\Step;
use App\Repositories\Contracts\ILeagueRepository;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class LeagueController extends Controller
{
    /**
     * Constructor of LeagueController.
     */
    public function __construct(protected ILeagueRepository $repository)
    {
        //
    }

    /**
     * Get all of leagues.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->repository->index(range(1,2)), Response::HTTP_OK);
    }

    /**
     * Get the league info.
     * @param League $league
     * @return JsonResponse
     */
    public function getLeagueInfo(League $league): JsonResponse
    {
        return response()->json($this->repository->getLeagueInfo($league), Response::HTTP_OK);
    }

    /**
     * Get the league info.
     * @param Step $step
     * @return JsonResponse
     */
    public function getStepInfo(Step $step): JsonResponse
    {
        return response()->json($this->repository->getStepInfo($step), Response::HTTP_OK);
    }
}
