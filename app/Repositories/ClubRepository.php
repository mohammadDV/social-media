<?php

namespace App\Repositories;

use App\Http\Requests\ClubRequest;
use App\Http\Requests\ClubUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Club;
use App\Models\Country;
use App\Models\League;
use App\Models\Matches;
use App\Models\Post;
use App\Models\Sport;
use App\Models\User;
use App\Repositories\Contracts\IClubRepository;
use App\Repositories\traits\GlobalFunc;
use App\Services\File\FileService;
use App\Services\Image\ImageService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class ClubRepository implements IClubRepository {

    use GlobalFunc;

    /**
     * @param ImageService $imageService
     * @param FileService $fileService
     */
    public function __construct(protected ImageService $imageService, protected FileService $fileService)
    {

    }

    /**
     * Get the clubs.
     * @param Sport|null $sport
     * @param Country|null $country
     * @return Collection
     */
    public function index(Sport|null $sport, Country|null $country) :Collection
    {
        return Club::query()
            ->when(Auth::user()->level != 3, function ($query) {
                return $query->where('user_id', Auth::user()->id);
            })
            ->when(!empty($sport->id), function ($query) use ($sport) {
                return $query->where('sport_id', $sport->id);
            })
            ->when(!empty($country->id), function ($query) use ($country) {
                return $query->where('country_id', $country->id);
            })
            ->with('sport','country')
            ->orderBy('title', 'ASC')
            ->get();
    }

    /**
     * Get the clubs pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator
    {
        $search = $request->get('query');
        return Club::query()
            ->when(Auth::user()->level != 3, function ($query) {
                return $query->where('user_id', Auth::user()->id);
            })
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('alias_title','like','%' . $search . '%');
            })
            ->with('sport','country')
            ->orderBy($request->get('sortBy', 'id'), $request->get('sortType', 'desc'))
            ->paginate($request->get('rowsPerPage', 25));
    }

    /**
     * Get the clubs followers pagination.
     * @param TableRequest $request
     * @param Club $club
     * @return LengthAwarePaginator
     */
    public function getFollowers(TableRequest $request, Club $club) :LengthAwarePaginator
    {
        $search = $request->get('query');
        return User::query()
            ->with('clubs')
            ->where('status', 1)
            ->where('level', '!=', 3)
            ->whereHas('clubs', function ($query) use($club) {
                return $query->where('club_id', $club->id);
            })
            ->orderBy($request->get('sortBy', 'id'), $request->get('sortType', 'desc'))
            ->paginate($request->get('rowsPerPage', 20));
    }

    /**
     * Get the club info.
     * @param Club $club
     * @return Club
     * @throws Exception
     */
    public function getInfo(Club $club) {

        if ($club->status != 1) {
            throw new Exception;
        }

        $result['info'] = $club->with('sport')->first();
        $result['posts'] = Post::query()
            ->where('status', 1)
            ->whereHas('tags', function ($query) use($club){
                $query->where('title', trim($club->title));
            })
            ->limit(6)
            ->get();

        $result['videos'] = Post::query()
            ->where('status', 1)
            ->where('type', 1)
            ->whereHas('tags', function ($query) use($club){
                $query->where('title', trim($club->title));
            })
            ->limit(6)
            ->get();

        $league = League::query()
            ->with('clubs')
            ->where('status', 1)
            ->where('type', 1)
            ->whereHas('clubs', function ($query) use($club){
                $query->where('id', $club->id);
            })
            ->orderBy('id', 'desc')
            ->first();

        $result['clubs'] = $league->clubs;

        $result['matches'] = Matches::query()
            ->with('teamHome', 'teamAway', 'step')
            ->where('status', 2)
            ->where(function ($query) use($club) {
                $query->where('home_id', $club->id)
                ->orWhere('away_id', $club->id);
            })
            ->orderBy('date', 'desc')
            ->limit(6)
            ->get();

        return $result;
    }

    /**
     * Get the club.
     * @param Club $club
     * @return Club
     */
    public function show(Club $club) :Club
    {
        return Club::query()
                ->with('sport','country')
                ->where('id', $club->id)
                ->first();
    }

    /**
     * Store the club.
     * @param ClubRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(ClubRequest $request) :JsonResponse
    {
        $this->checkLevelAccess();

        $club = Club::create([
            'alias_id'      => $request->input('alias_id'),
            'alias_title'   => $request->input('alias_title'),
            'title'         => $request->input('title'),
            'image'         => $request->input('image'),
            'country_id'    => $request->input('country_id'),
            'sport_id'      => $request->input('sport_id'),
            'user_id'       => Auth::user()->id,
            'status'        => $request->input('status'),
        ]);

        if ($club) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();
    }

    /**
     * Update the club.
     * @param ClubUpdateRequest $request
     * @param Club $club
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(ClubUpdateRequest $request, Club $club) :JsonResponse
    {
        $this->checkLevelAccess(Auth::user()->id == $club->user_id);

        $club = $club->update([
            'alias_id'      => $request->input('alias_id'),
            'alias_title'   => $request->input('alias_title'),
            'title'         => $request->input('title'),
            'image'         => $request->input('image'),
            'country_id'    => $request->input('country_id'),
            'sport_id'      => $request->input('sport_id'),
            'user_id'       => auth()->user()->id,
            'status'        => $request->input('status'),
        ]);

        if ($club) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
    }

    /**
    * Delete the club.
    * @param UpdatePasswordRequest $request
    * @param Club $club
    * @return JsonResponse
    */
   public function destroy(Club $club) :JsonResponse
   {
        $this->checkLevelAccess(Auth::user()->id == $club->user_id);

        $club->delete();

        if ($club) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
   }
}
