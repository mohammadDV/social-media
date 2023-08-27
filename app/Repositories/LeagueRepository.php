<?php

namespace App\Repositories;

use App\Services\Image\ImageService;
use App\Services\MatchService;
use App\Models\League;
use App\Repositories\Contracts\ILeagueRepository;
use Illuminate\Http\Client\Request;

class LeagueRepository extends MatchService implements ILeagueRepository {

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

        $data['step']       = $this->getSteps($league->id ?? 0);
        $data['matches']    = $this->getMatches($data['step']['current']->id ?? 0);
        if($league->type == 1){
            $data['clubs']      = $this->getClubs($league->id ?? 0);
        }else{
            $data['clubs']      = $this->getTournamentClubs($data['step']['current']->id ?? 0);
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
     * Get all of data.
     * @param Request $request
     * @return League
     */
    public function store(Request $request) :League
    {

        $imageService   = new ImageService();
        $imageResult    = null;
        if ($request->hasFile('image')){
            $imageService->setExclusiveDirectory('uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'leagues');
            $imageResult = $imageService->save($request->file('image'));
        }

        return League::create([
            'title'         => $request->input('title'),
            'image'         => $imageResult,
            'country_id'    => $request->input('country_id'),
            'sport_id'      => $request->input('sport_id'),
            'user_id'       => auth()->user()->id,
            'status'        => $request->input('status'),
            'type'          => $request->input('type')
        ]);

    }

    /**
     * Update the league.
     * @param Request $request
     * @param League $league
     * @return bool
     */
    public function update(Request $request, League $league) :bool {

        $imageService   = new ImageService();
        $imageResult    = $league->image;
        if ($request->hasFile('image')){
            $imageService->setExclusiveDirectory('uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'leagues');
            $imageResult = $imageService->save($request->file('image'));
            if ($imageResult && !empty($league->image)){
                $imageService->deleteImage($league->image);
            }
        }

        return $league->update([
            'title'         => $request->input('title'),
            'image'         => $imageResult,
            'country_id'    => $request->input('country_id'),
            'sport_id'      => $request->input('sport_id'),
            'user_id'       => auth()->user()->id,
            'status'        => $request->input('status'),
            'type'          => $request->input('type')
        ]);

    }

    /**
     * Get the league.
     * @param Request $request
     * @return array
     */
    public function get(Request $request) :array
    {
        $draw               = $request->get('draw');
        $start              = $request->get('start');
        $rowperpage         = $request->get('length');
        $columnIndex_arr    = $request->get('order');
        $columnName_arr     = $request->get('columns');
        $order_arr          = $request->get('order');
        $search_arr         = $request->get('search');
        $columnIndex        = $columnIndex_arr[0]['column'];
        $columnName         = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder    = $order_arr[0]['dir'];
        $searchValue        = $search_arr['value'];

        $totalRecords = League::select('count(*) as allcount')->count();

        $totalRecordsWithFilter = League::select('count(*) as allcount')
            ->where('title','like','%' . $searchValue . '%')
            ->orWhere('alias_title','like','%' . $searchValue . '%')
            ->count();

        $records = League::with('sport','country')->orderBy($columnName,$columnSortOrder)
            ->where('title','like','%' . $searchValue . '%')
            ->orWhere('alias_title','like','%' . $searchValue . '%')
            ->skip($start)
            ->take($rowperpage)
            ->get();


        $data_arr = [];
        foreach ($records as $record) {
            $data_arr[] = [
                'id'            => $record->id,
                'title'         => $record->title,
                'user_id'       => $record->user_id,
                'sport'         => $record->sport->title,
                'country'       => $record->country->title,
                'type'          => $record->type_name,
                'status'        => $record->status_name,
                'created_at'    => $record->created_at->diffforhumans(),
            ];
        }

        return [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordsWithFilter,
            "aaData" => $data_arr
        ];

    }
}
