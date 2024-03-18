<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\SearchClubRequest;
use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IFavoriteRepository.
 */
interface IFavoriteRepository  {

    /**
     * Get favorite clubs of the user
     * @param ?User $user
     * @return Collection
     */
    public function getClubs(?User $user) :Collection;

    /**
     * Get favorite clubs of the user with limitation
     * @param ?User $user
     * @return Collection
     */
    public function getClubsLimited(?User $user) :Collection;

    /**
     * Store club of the user
     * @param Club $club
     * @return JsonResponse
     */
    public function storeClub(Club $club) :JsonResponse;

    /**
     * Search to choose favorite club for the user
     * @param SearchClubRequest $request
     * @return LengthAwarePaginator
     */
    public function search(SearchClubRequest $request) :LengthAwarePaginator;


}
