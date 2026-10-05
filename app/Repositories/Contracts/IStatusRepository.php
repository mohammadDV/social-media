<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\StatusRequest;
use App\Http\Requests\StatusUpdateRequest;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface IStatusRepository.
 */
interface IStatusRepository
{
    /**
     * Get the status.
     */
    public function index(?User $user);

    /**
     * Get the status.
     *
     * @param  ?User  $user
     */
    public function getAllPerUser(User $user): LengthAwarePaginator;

    /**
     * Get favorites.
     */
    public function getFavorite(User $user): LengthAwarePaginator;

    /**
     * Add the status to favorites.
     */
    public function addFavorite(Status $status): JsonResponse;

    /**
     * Get the status info.
     *
     * @return StatusResource
     */
    public function getInfo(Status $status);

    /**
     * Get all statuses.
     */
    public function statusPaginate(Request $request): LengthAwarePaginator;

    /**
     * Store the status.
     */
    public function store(StatusRequest $request): JsonResponse;

    /**
     * Update the status.
     *
     * @throws \Exception
     */
    public function update(StatusUpdateRequest $request, Status $status): JsonResponse;

    /**
     * Delete the status.
     *
     * @throws \Exception
     */
    public function destroy(Status $status): JsonResponse;

    /**
     * Delete completely the status.
     */
    public function realDestroy(int $id): JsonResponse;
}
