<?php

namespace App\Repositories;

use App\Http\Requests\PlayerRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Player;
use App\Models\Country;
use App\Models\League;
use App\Models\Matches;
use App\Models\Post;
use App\Models\Sport;
use App\Models\Tag;
use App\Repositories\Contracts\IPlayerRepository;
use App\Repositories\traits\GlobalFunc;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PlayerRepository implements IPlayerRepository {

    use GlobalFunc;

    /**
     * Get the players.
     * @param Sport|null $sport
     * @param Country|null $country
     * @return Collection
     */
    public function index(Sport|null $sport, Country|null $country) :Collection
    {
        return Player::query()
            ->when(Auth::user()->level != 3, function ($query) {
                return $query->where('user_id', Auth::user()->id);
            })
            ->when(!empty($sport->id), function ($query) use ($sport) {
                return $query->where('sport_id', $sport->id);
            })
            ->when(!empty($country->id), function ($query) use ($country) {
                return $query->where('country_id', $country->id);
            })
            ->with('sport','country', 'club')
            ->orderBy('title', 'ASC')
            ->get();
    }

    /**
     * Get the players pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator
    {
        $search = $request->get('query');
        return Player::query()
            ->when(Auth::user()->level != 3, function ($query) {
                return $query->where('user_id', Auth::user()->id);
            })
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('alias_title','like','%' . $search . '%');
            })
            ->with('sport','country', 'club')
            ->orderBy($request->get('sortBy', 'id'), $request->get('sortType', 'desc'))
            ->paginate($request->get('rowsPerPage', 25));
    }

    /**
     * Get the player info.
     * @param Player $player
     * @return Player
     * @throws Exception
     */
    public function getInfo(Player $player) {


        if ($player->status != 1) {
            throw new Exception;
        }

        $player = Player::query()
            ->with('sport')
            ->where('id', $player->id)
            ->first();

        $tag = trim($player->title);

        if ($player?->sport_id != 1) {
            $tag = trim($player?->sport?->title . ' ' . $player->title);
        }

        $tag = Tag::firstOrCreate(['title' => $tag]);

        $info = $player;

        $posts = Post::query()
            ->where('status', 1)
            ->where('type', 0)
            ->whereHas('tags', function ($query) use($tag){
                $query->where('id', $tag->id);
            })
            ->limit(6)
            ->get();

        $videos = Post::query()
            ->where('status', 1)
            ->where('type', 1)
            ->whereHas('tags', function ($query) use($tag){
                $query->where('id', $tag->id);
            })
            ->limit(6)
            ->get();

        $league = League::query()
            ->with('players')
            ->where('status', 1)
            ->where('type', 1)
            ->whereHas('players', function ($query) use($player){
                $query->where('id', $player->id);
            })
            ->orderBy('id', 'desc')
            ->first();

        $players = $league?->players;

        $matches = Matches::query()
            ->with('teamHome', 'teamAway', 'step')
            ->where('status', 2)
            ->where(function ($query) use($player) {
                $query->where('home_id', $player->id)
                ->orWhere('away_id', $player->id);
            })
            ->orderBy('date', 'desc')
            ->limit(6)
            ->get();

        return [
            'tag' => $tag,
            'info' => $info,
            'posts' => $posts,
            'videos' => $videos,
            'players' => $players,
            'matches' => $matches,
        ];
    }

    /**
     * Get the player.
     * @param Player $player
     * @return Player
     */
    public function show(Player $player) :Player
    {
        return Player::query()
                ->with('sport','country', 'club')
                ->where('id', $player->id)
                ->first();
    }

    /**
     * Store the player.
     * @param PlayerRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(PlayerRequest $request) :JsonResponse
    {
        $this->checkLevelAccess();

        $player = Player::create($request->all());

        if ($player) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();
    }

    /**
     * Update the player.
     * @param PlayerRequest $request
     * @param Player $player
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(PlayerRequest $request, Player $player) :JsonResponse
    {
        $this->checkLevelAccess();

        $player = $player->update($request->except('sport', 'country', 'club'));

        if ($player) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
    }

    /**
    * Delete the player.
    * @param UpdatePasswordRequest $request
    * @param Player $player
    * @return JsonResponse
    */
   public function destroy(Player $player) :JsonResponse
   {
        $this->checkLevelAccess(Auth::user()->id == $player->user_id);

        $player->delete();

        if ($player) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
   }
}