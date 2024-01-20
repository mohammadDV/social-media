<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\LikeStoreRequest;
use App\Http\Requests\SearchClubRequest;
use App\Models\Club;
use App\Models\User;
use App\Repositories\Contracts\IFavoriteRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class FavoriteController extends Controller
{
    /**
     * Constructor of PostController.
     */
    public function __construct(protected IFavoriteRepository $repository)
    {
        //
    }

    /**
     * Get favorite clubs of the user
     * @param ?User $user
     * @return JsonResponse
     */
    public function getClubs(?User $user): JsonResponse
    {
        return response()->json($this->repository->getClubs($user), Response::HTTP_OK);
    }

    /**
     * Store favorite club for the user
     * @param Club $request
     * @return JsonResponse
     */
    public function storeClub(Club $club): JsonResponse
    {
        return $this->repository->storeClub($club);
    }

    /**
     * Search to choose favorite club for the user
     * @param SearchClubRequest $request
     * @return JsonResponse
     */
    public function search(SearchClubRequest $request): LengthAwarePaginator
    {
        return $this->repository->search($request);
    }
}
