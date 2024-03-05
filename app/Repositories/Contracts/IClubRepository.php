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
interface IClubRepository  {

    /**
     * Get the clubs pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator;

     /**
     * Get the clubs.
     * @param Sport|null $sport
     * @param Country|null $country
     * @return Collection
     */
    public function index(Sport|null $sport, Country|null $country) :Collection;

    /**
     * Get the club info.
     * @param Club $club
     * @return Club
     */
    public function getInfo(Club $club);

    /**
     * Get the clubs followers pagination.
     * @param TableRequest $request
     * @param Club $club
     * @return LengthAwarePaginator
     */
    public function getFollowers(TableRequest $request, Club $club) :LengthAwarePaginator;

    /**
     * Get the club.
     * @param Club $club
     * @return Club
     */
    public function show(Club $club) :Club;

    /**
     * Store the club.
     * @param ClubRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(ClubRequest $request) :JsonResponse;

    /**
     * Update the club.
     * @param ClubUpdateRequest $request
     * @param Club $club
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(ClubUpdateRequest $request, Club $club) :JsonResponse;

    /**
    * Delete the club.
    * @param UpdatePasswordRequest $request
    * @param Club $club
    * @return JsonResponse
    */
   public function destroy(Club $club) :JsonResponse;

}
