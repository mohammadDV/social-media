<?php

namespace App\Repositories;

use App\Http\Requests\SearchClubRequest;
use App\Models\Club;
use App\Models\FavoriteClub;
use App\Models\User;
use App\Repositories\Contracts\IFavoriteRepository;
use App\Repositories\traits\GlobalFunc;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class FavoriteRepository implements IFavoriteRepository {

    use GlobalFunc;

    /**
     * Get favorite clubs of the user
     * @param ?User $user
     * @return Collection
     */
    public function getClubs(?User $user) :Collection
    {
        if ($this->isUserBlocked($user)) {
            return new Collection();
        }

        return !empty($user->id) ? $user->clubs : Auth::user()->clubs;
    }

    /**
     * Get favorite clubs of the user with limitation
     * @param ?User $user
     * @return Collection
     */
    public function getClubsLimited(?User $user) :Collection
    {
        return !empty($user->id) ? $user->clubsLimited : Auth::user()->clubsLimited;
    }

    /**
     * Store club of the user
     * @param Club $club
     * @return JsonResponse
     */
    public function storeClub(Club $club) :JsonResponse
    {
        if (!$club->status) {
            throw new Exception;
        }

        $FavoriteClub = FavoriteClub::query()
            ->where('club_id', $club->id)
            ->where('user_id', Auth::user()->id)
            ->first();

        if ($FavoriteClub) {
            $FavoriteClub->delete();
            return response()->json([
                'status' => 1,
                'active' => 0,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        if (FavoriteClub::query()
            ->where('user_id', Auth::user()->id)->count() >= config('favorite.maximum_favorite_clubs')) {
                return response()->json([
                    'status' => 0,
                    'active' => 0,
                    'message' => __('site.You have already selected your favorite clubs. Please remove previous selections to replace it.')
                ], Response::HTTP_OK);
        }

        $FavoriteClub = FavoriteClub::create([
            'club_id'       => $club->id,
            'user_id'       => auth()->user()->id,
        ]);

        if ($FavoriteClub) {
            return response()->json([
                'status' => 1,
                'active' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }
    }

    /**
     * Search to choose favorite club for the user
     * @param SearchClubRequest $request
     * @return LengthAwarePaginator
     */
    public function search(SearchClubRequest $request) :LengthAwarePaginator
    {
        $userClubs = Auth::user()->clubs;

        $clubs = Club::query()
            ->with('sport','country')
            ->where('status', '=', 1)
            ->where('sport_id', $request->sport_id)
            ->when(!empty($userClubs) , function ($query) use ($userClubs) {
                $query->whereNotIn('id', $userClubs->pluck('id'));
            })
            ->when(!empty($request->country_id) , function ($query) use ($request) {
                $query->where('country_id', $request->country_id);
            })
            ->when(!empty($request->search) , function ($query) use ($request) {
                $query->where('title', "like", "%" . $request->search . "%");
                $query->orWhere('alias_title', "like", "%" . $request->search . "%");
            })
            ->orderBy('id', 'DESC')->paginate(12);

        return $clubs;
    }

}
