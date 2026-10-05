<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\LeagueRequest;
use App\Http\Requests\LeagueUpdateRequest;
use App\Http\Requests\StoreClubRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\League;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface ILeagueRepository.
 */
interface ILeagueRepository
{
    /**
     * Get the leagues.
     */
    public function index(): array;

    /**
     * Get the league info.
     */
    public function getLeagueInfo(League $league): array;

    /**
     * Get the table of the league info.
     */
    public function getTableLeague(): array;

    /**
     * Get the leagues pagination.
     */
    public function indexPaginate(TableRequest $request): LengthAwarePaginator;

    /**
     * Get the league.
     */
    public function show(League $league): League;

    /**
     * Store the league.
     *
     * @throws \Exception
     */
    public function store(LeagueRequest $request): JsonResponse;

    /**
     * Update the league.
     *
     * @throws \Exception
     */
    public function update(LeagueUpdateRequest $request, League $league): JsonResponse;

    /**
     * Delete the league.
     *
     * @param  UpdatePasswordRequest  $request
     */
    public function destroy(League $league): JsonResponse;

    /**
     * Store the club to the league.
     */
    public function storeClubs(StoreClubRequest $request, League $league): JsonResponse;

    /**
     * Get the clubs of league.
     *
     * @return collectoin
     */
    public function getClubs(League $league): Collection;

    /**
     * Get the steps of league.
     *
     * @return collectoin
     */
    public function getAllSteps(League $league): Collection;
}
