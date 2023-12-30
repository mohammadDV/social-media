<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeagueRequest;
use App\Http\Requests\LeagueUpdateRequest;
use App\Http\Requests\StoreClubRequest;
use App\Http\Requests\TableRequest;
use App\Models\League;
use App\Repositories\Contracts\ILeagueRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LeagueController extends Controller
{
    /**
     * Constructor of LeagueController.
     */
    public function __construct(protected  ILeagueRepository $repository)
    {
        //
    }

    /**
     * Get all of leagues with pagination
     * @param TableRequest $request
     * @return JsonResponse
     */
    public function indexPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get the league.
     * @param
     * @return JsonResponse
     */
    public function show(League $league) :JsonResponse
    {
        return response()->json($this->repository->show($league), Response::HTTP_OK);
    }

    /**
     * Store the league.
     * @param LeagueRequest $request
     * @return JsonResponse
     */
    public function store(LeagueRequest $request) :JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Update the league.
     * @param LeagueUpdateRequest $request
     * @param League $league
     * @return JsonResponse
     */
    public function update(LeagueUpdateRequest $request, League $league) :JsonResponse
    {
        return $this->repository->update($request, $league);
    }

    /**
     * Delete the league.
     * @param League $league
     * @return JsonResponse
     */
    public function destroy(League $league) :JsonResponse
    {
        return $this->repository->destroy($league);
    }

    /**
     * Store the club to the league.
     * @param League $league
     * @return JsonResponse
     */
    public function storeClubs(StoreClubRequest $request, League $league) :JsonResponse
    {
        return $this->repository->storeClubs($request, $league);
    }

     /**
    * Get the clubs of league.
    * @param League $league
    * @return JsonResponse
    */
    public function getClubs(League $league) :JsonResponse
    {
        return response()->json($this->repository->getClubs($league), Response::HTTP_OK);
    }

     /**
    * Get the steps of league.
    * @param League $league
    * @return JsonResponse
    */
    public function getAllSteps(League $league) :JsonResponse
    {
        return response()->json($this->repository->getAllSteps($league), Response::HTTP_OK);
    }
}
