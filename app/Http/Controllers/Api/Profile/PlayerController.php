<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerRequest;
use App\Http\Requests\TableRequest;
use App\Models\Player;
use App\Models\Country;
use App\Models\Sport;
use App\Repositories\Contracts\IPlayerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PlayerController extends Controller
{
    /**
     * Constructor of PlayerController.
     */
    public function __construct(protected  IPlayerRepository $repository)
    {
        //
    }

    /**
     * Get all of players with pagination
     * @param Sport $sport
     * @param Country $country
     * @return JsonResponse
     */
    public function index(?Sport $sport, ?Country $country): JsonResponse
    {
        return response()->json($this->repository->index($sport, $country), Response::HTTP_OK);
    }

    /**
     * Get all of players with pagination
     * @param TableRequest $request
     * @return JsonResponse
     */
    public function indexPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get the player.
     * @param
     * @return JsonResponse
     */
    public function show(Player $player) :JsonResponse
    {
        return response()->json($this->repository->show($player), Response::HTTP_OK);
    }

    /**
     * Store the player.
     * @param PlayerRequest $request
     * @return JsonResponse
     */
    public function store(PlayerRequest $request) :JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Update the player.
     * @param PlayerRequest $request
     * @param Player $player
     * @return JsonResponse
     */
    public function update(PlayerRequest $request, Player $player) :JsonResponse
    {
        return $this->repository->update($request, $player);
    }

    /**
     * Does the user follow the player or not.
     * @param Player $player
     * @return JsonResponse
     */
    public function isActive(Player $player) :JsonResponse
    {
        return response()->json($this->repository->isActive($player), Response::HTTP_OK);
    }

    /**
     * Delete the player.
     * @param Player $player
     * @return JsonResponse
     */
    public function destroy(Player $player) :JsonResponse
    {
        return $this->repository->destroy($player);
    }
}