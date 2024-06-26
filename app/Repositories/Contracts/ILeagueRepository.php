<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\LeagueRequest;
use App\Http\Requests\LeagueUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\StoreClubRequest;
use App\Models\League;
use App\Models\Step;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface ILeagueRepository.
 */
interface ILeagueRepository  {

    /**
     * Get the leagues.
     * @return array
     */
    public function index() :array;

    /**
     * Get the league info.
     * @param League $league
     * @return array
     */
    public function getLeagueInfo(League $league) :array;

    /**
     * Get the table of the league info.
     * @return array
     */
    public function getTableLeague() :array;

    /**
     * Get the leagues pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator;

    /**
     * Get the league.
     * @param League $league
     * @return League
     */
    public function show(League $league) :League;

    /**
     * Store the league.
     * @param LeagueRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(LeagueRequest $request) :JsonResponse;

    /**
     * Update the league.
     * @param LeagueUpdateRequest $request
     * @param League $league
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(LeagueUpdateRequest $request, League $league) :JsonResponse;

    /**
    * Delete the league.
    * @param UpdatePasswordRequest $request
    * @param League $league
    * @return JsonResponse
    */
   public function destroy(League $league) :JsonResponse;

    /**
    * Store the club to the league.
    * @param StoreClubRequest $request
    * @param League $league
    * @return JsonResponse
    */
   public function storeClubs(StoreClubRequest $request, League $league) :JsonResponse;

    /**
    * Get the clubs of league.
    * @param League $league
    * @return collectoin
    */
   public function getClubs(League $league) :Collection;

    /**
    * Get the steps of league.
    * @param League $league
    * @return collectoin
    */
    public function getAllSteps(League $league) :Collection;

}