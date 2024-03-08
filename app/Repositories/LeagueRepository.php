<?php

namespace App\Repositories;

use App\Http\Requests\LeagueRequest;
use App\Http\Requests\LeagueUpdateRequest;
use App\Http\Requests\StoreClubRequest;
use App\Http\Requests\TableRequest;
use App\Models\Club;
use App\Services\Image\ImageService;
use App\Services\MatchService;
use App\Models\League;
use App\Repositories\Contracts\ILeagueRepository;
use App\Repositories\traits\GlobalFunc;
use App\Services\File\FileService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class LeagueRepository extends MatchService implements ILeagueRepository {

    use GlobalFunc;

    /**
     * @param ImageService $imageService
     * @param FileService $fileService
     */
    public function __construct(protected ImageService $imageService, protected FileService $fileService)
    {

    }

    /**
     * Get the leagues.
     * @param $sports
     * @return array
     */
    public function index(array $sports) :array
    {
        // ->addMinutes('1'),
        $leagueRows = cache()->remember("league.all." . implode(".",$sports), now(),
            function () use($sports) {
            return League::Query()
                ->with('sport')
                ->whereIn('sport_id', $sports)
                ->where('status',1)
                ->orderBy('priority','ASC')
                ->get();
        });

        $data       = [];
        $leagues    = [];
        $result     = [];
        foreach($leagueRows as $league) {
            if(empty($data[$league->sport_id])) {
                $data[$league->sport_id] = $league;
            }
            $leagues[$league->sport_id][] = $league;
        }
        foreach($data as $key => $item){

            $leagueInfo = $this->getLeagueInfo($item);

            $result[$key] = [
                "title"     => __('site.World ' . strtolower($item->sport->alias_title)),
                "leagues"   => $leagues[$key],
                "steps"     => $leagueInfo['steps'] ?? [],
                "matches"   => $leagueInfo['matches'] ?? [],
                "clubs"     => $leagueInfo['clubs'],
            ];
        }

        return $result;

    }

    /**
     * Get the league info.
     * @param League $league
     * @return array
     */
    public function getLeagueInfo(League $league) :array
    {

        $data['steps']       = $this->getSteps($league->id ?? 0);

        $data['matches']    = $this->getMatches($data['steps']['current']->id ?? 0);
        if($league->type == 1){
            $data['clubs']      = $this->getClubs($league);
        }else{
            $data['clubs']      = $this->getTournamentClubs($data['steps']['current']->id ?? 0);
        }

        return $data;

    }

    /**
     * Get all of data.
     * @param string $search
     * @param int $sport_id
     * @param int $country_id
     * @param array $favorites
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function searchClub(string $search,int $sport_id,int $country_id, array $favorites = [],int $limit = 200, int $offset = 0) :array
    {

        $result = [];
        $clubs  = League::with('sport','country')
        ->where([['status', 1],['sport_id',$sport_id],['country_id',$country_id]]);

        if(strlen($search) > 0) {
            $clubs->where(function ($query) use ($search) {
                $query->where('title', "like", "%" . $search . "%");
                $query->orWhere('alias_title', "like", "%" . $search . "%");
            });
        }

        $clubs = $clubs->take($limit)->skip($offset)->get();

        foreach($clubs ?? [] as $key => $item) {
            $result[$key]['id']         = $item->id;
            $result[$key]['title']      = $item->title;
            $result[$key]['sport']      = $item->sport->title;
            $result[$key]['country']    = $item->country->title;
            $result[$key]['image']      = !empty($item->image) ? asset($item->image) : asset('/assets/site/images/user-icon.png');

            if(in_array($result[$key]['id'], $favorites)) {
                $result[$key]['button'] = [
                    "url"   => "followClub",
                    "text"  => __('site.Unfollow'),
                    "class" => "btn-danger",
                ];
            }else{
                $result[$key]['button'] = [
                    "url"   => "followClub",
                    "text"  => __('site.Follow'),
                    "class" => "btn-primary",
                ];
            }
        }

        return $result;
    }

    /**
     * Get the clubs pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator
    {
        $search = $request->get('query');

        return League::query()
            ->with('sport','country')
            ->orderBy($request->get('column') ?? 'id', $request->get('sort') ?? 'desc')
            ->when(!empty($search), function ($query) use($search) {
                $query->where('title','like','%' . $search . '%')
                ->orWhere('alias_title','like','%' . $search . '%');
            })
            ->orderBy($request->get('sortBy', 'id'), $request->get('sortType', 'desc'))
            ->paginate($request->get('rowsPerPage', 25));
    }

    /**
     * Get the League.
     * @param League $league
     * @return League
     */
    public function show(League $league) :League
    {
        return League::query()
                ->with('sport','country')
                ->where('id', $league->id)
                ->first();
    }

    /**
    * Delete the club.
    * @param UpdatePasswordRequest $request
    * @param League $league
    * @return JsonResponse
    */
   public function destroy(League $league) :JsonResponse
   {
        $this->checkLevelAccess(Auth::user()->id == $league->user_id);

        $league->delete();

        if ($league) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
   }

    /**
     * Store the league.
     * @param LeagueRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(LeagueRequest $request) :JsonResponse
    {
        $this->checkLevelAccess();

        $league = League::create([
            'alias_id'      => $request->input('alias_id'),
            'alias_title'   => $request->input('alias_title'),
            'title'         => $request->input('title'),
            'image'         => $request->input('image'),
            'country_id'    => $request->input('country_id'),
            'sport_id'      => $request->input('sport_id'),
            'user_id'       => auth()->user()->id,
            'status'        => $request->input('status'),
            'type'          => $request->input('type'),
            'priority'      => $request->input('priority', 0)
        ]);


        if ($league) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();

    }

    /**
     * Update the league.
     * @param LeagueUpdateRequest $request
     * @param League $league
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(LeagueUpdateRequest $request, League $league) :JsonResponse
    {
        $this->checkLevelAccess(Auth::user()->id == $league->user_id);

        $league = $league->update([
            'alias_id'      => $request->input('alias_id'),
            'alias_title'   => $request->input('alias_title'),
            'title'         => $request->input('title'),
            'image'         => $request->input('image'),
            'country_id'    => $request->input('country_id'),
            'sport_id'      => $request->input('sport_id'),
            'user_id'       => auth()->user()->id,
            'status'        => $request->input('status'),
            'type'          => $request->input('type'),
            'priority'      => $request->input('priority', 0)
        ]);


        if ($league) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();

    }

    /**
    * Get the clubs of league.
    * @param League $league
    * @return collectoin
    */
    public function getClubs(League $league) :Collection
    {
        return League::find($league->id)->clubs;
    }

    /**
    * Get the steps of league.
    * @param League $league
    * @return collectoin
    */
    public function getAllSteps(League $league) :Collection
    {
        return League::find($league->id)->steps;
    }

    /**
    * Store the club to the league.
    * @param StoreClubRequest $request
    * @param League $league
    * @return JsonResponse
    */
    public function storeClubs(StoreClubRequest $request, League $league) :JsonResponse
    {
        $league->clubs()->sync($request->all());

        return response()->json([
            'status' => 1,
            'message' => __('site.Clubs has been stored')
        ], Response::HTTP_OK);
    }
}
