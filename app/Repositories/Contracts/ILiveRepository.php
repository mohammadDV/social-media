<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\LiveRequest;
use App\Http\Requests\TableRequest;
use App\Models\Live;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Interface ILiveRepository.
 */
interface ILiveRepository
{
    /**
     * Get the lives.
     */
    public function index(): array;

    /**
     * Get the lives pagination.
     */
    public function indexPaginate(TableRequest $request): LengthAwarePaginator;

    /**
     * Get the live.
     */
    public function show(Live $live): Live;

    /**
     * Store the live.
     *
     * @throws \Exception
     */
    public function store(LiveRequest $request): JsonResponse;

    /**
     * Update the live.
     *
     * @throws \Exception
     */
    public function update(LiveRequest $request, Live $live): JsonResponse;

    /**
     * Delete the live.
     *
     * @param  UpdatePasswordRequest  $request
     */
    public function destroy(Live $live): JsonResponse;
}
