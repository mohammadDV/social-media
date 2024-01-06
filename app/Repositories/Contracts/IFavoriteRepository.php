<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\SearchClubRequest;
use App\Models\Club;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IFavoriteRepository.
 */
interface IFavoriteRepository  {

    /**
     * Get favorite clubs of the user
     * @return Collection
     */
    public function getClubs() :Collection;

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
