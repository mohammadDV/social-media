<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\Step;
use App\Repositories\Contracts\ILeagueRepository;
use App\Repositories\Contracts\IStepRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LeagueController extends Controller
{
    /**
     * Constructor of LeagueController.
     */
    public function __construct(protected ILeagueRepository $repository, protected IStepRepository $stepRepository)
    {
        //
    }

    /**
     * Get all of leagues.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->repository->index(), Response::HTTP_OK);
    }

    /**
     * Get the league info.
     */
    public function getLeagueInfo(League $league): JsonResponse
    {
        return response()->json($this->repository->getLeagueInfo($league), Response::HTTP_OK);
    }

    /**
     * Get the league info.
     */
    public function getStepInfo(Step $step): JsonResponse
    {
        return response()->json($this->stepRepository->getStepInfo($step), Response::HTTP_OK);
    }
}
