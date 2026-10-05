<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\SportRequest;
use App\Http\Requests\SportUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Models\Sport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface ISportRepository.
 */
interface ISportRepository
{
    /**
     * Get the sports pagination.
     */
    public function indexPaginate(TableRequest $request): LengthAwarePaginator;

    /**
     * Get the sports pagination.
     */
    public function index(): Collection;

    /**
     * Get the sport.
     */
    public function show(Sport $sport): Sport;

    /**
     * Store the sport.
     *
     * @throws \Exception
     */
    public function store(SportRequest $request): JsonResponse;

    /**
     * Update the sport.
     *
     * @throws \Exception
     */
    public function update(SportUpdateRequest $request, Sport $sport): JsonResponse;

    /**
     * Delete the sport.
     */
    public function destroy(Sport $sport): JsonResponse;
}
