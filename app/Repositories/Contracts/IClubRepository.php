<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\ClubRequest;
use App\Http\Requests\ClubUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Club;
use App\Models\Country;
use App\Models\Sport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface IClubRepository.
 */
interface IClubRepository
{
    /**
     * Get the clubs pagination.
     */
    public function indexPaginate(TableRequest $request): LengthAwarePaginator;

    /**
     * Get the clubs.
     */
    public function index(?Sport $sport, ?Country $country): Collection;

    /**
     * Get the club info.
     *
     * @return Club
     */
    public function getInfo(Club $club);

    /**
     * Get the clubs followers pagination.
     */
    public function getFollowers(TableRequest $request, Club $club): LengthAwarePaginator;

    /**
     * Get the club.
     */
    public function show(Club $club): Club;

    /**
     * Store the club.
     *
     * @throws \Exception
     */
    public function store(ClubRequest $request): JsonResponse;

    /**
     * Update the club.
     *
     * @throws \Exception
     */
    public function update(ClubUpdateRequest $request, Club $club): JsonResponse;

    /**
     * Does the user follow the club or not.
     *
     * @throws \Exception
     */
    public function isActive(Club $club): array;

    /**
     * Delete the club.
     *
     * @param  UpdatePasswordRequest  $request
     */
    public function destroy(Club $club): JsonResponse;
}
