<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\PlayerRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Player;
use App\Models\Country;
use App\Models\Sport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IPlayerRepository.
 */
interface IPlayerRepository  {

    /**
     * Get the players pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator;

     /**
     * Get the players.
     * @param Sport|null $sport
     * @param Country|null $country
     * @return Collection
     */
    public function index(Sport|null $sport, Country|null $country) :Collection;

    /**
     * Get the player info.
     * @param Player $player
     * @return Player
     */
    public function getInfo(Player $player);

    /**
     * Get the player.
     * @param Player $player
     * @return Player
     */
    public function show(Player $player) :Player;

    /**
     * Store the player.
     * @param PlayerRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(PlayerRequest $request) :JsonResponse;

    /**
     * Update the player.
     * @param PlayerRequest $request
     * @param Player $player
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(PlayerRequest $request, Player $player) :JsonResponse;

    /**
    * Delete the player.
    * @param UpdatePasswordRequest $request
    * @param Player $player
    * @return JsonResponse
    */
   public function destroy(Player $player) :JsonResponse;

}