<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\PlayerRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Country;
use App\Models\Player;
use App\Models\Sport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface IPlayerRepository.
 */
interface IPlayerRepository
{
    /**
     * Get the players pagination.
     */
    public function indexPaginate(TableRequest $request): LengthAwarePaginator;

    /**
     * Get the players.
     */
    public function index(?Sport $sport, ?Country $country): Collection;

    /**
     * Get the player info.
     *
     * @return Player
     */
    public function getInfo(Player $player);

    /**
     * Get the player.
     */
    public function show(Player $player): Player;

    /**
     * Store the player.
     *
     * @throws \Exception
     */
    public function store(PlayerRequest $request): JsonResponse;

    /**
     * Update the player.
     *
     * @throws \Exception
     */
    public function update(PlayerRequest $request, Player $player): JsonResponse;

    /**
     * Delete the player.
     *
     * @param  UpdatePasswordRequest  $request
     */
    public function destroy(Player $player): JsonResponse;
}
