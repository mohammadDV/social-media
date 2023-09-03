<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\StatusRequest;
use App\Http\Requests\StatusUpdateRequest;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IStatusRepository.
 */
interface IStatusRepository  {

    /**
     * Get the status.
     * @param ?User $user
     * @return LengthAwarePaginator
     */
    public function index(?User $user) :LengthAwarePaginator;

    /**
     * Get the status info.
     * @param Status $status
     * @return StatusResource
     */
    // public function getStatusInfo(Status $status) :StatusResource;

    /**
     * Get all statuses.
     * @return LengthAwarePaginator
     */
    public function statusPaginate() :LengthAwarePaginator;

    /**
     * Store the status.
     *
     * @param  StatusRequest  $request
     * @return JsonResponse
     */
    public function store(StatusRequest $request) :JsonResponse;

    /**
     * Update the status.
     *
     * @param  StatusUpdateRequest  $request
     * @param  Status  $status
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(StatusUpdateRequest $request, Status $status) :JsonResponse;

    /**
     * Delete the status.
     *
     * @param  Status  $status
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(Status $status) :JsonResponse;

    /**
     * Delete completely the status.
     * @param int $id
     * @return JsonResponse
     */
    public function realDestroy(int $id): JsonResponse;

}
