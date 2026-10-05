<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\StepRequest;
use App\Http\Requests\StoreClubRequest;
use App\Models\League;
use App\Models\Step;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

/**
 * Interface IStepRepository.
 */
interface IStepRepository
{
    /**
     * Get the step info.
     */
    public function getStepInfo(Step $step): array;

    /**
     * Get the step.
     */
    public function show(Step $step): Step;

    /**
     * Store the step.
     */
    public function store(StepRequest $request, League $league): JsonResponse;

    /**
     * Update the step.
     *
     * @throws \Exception
     */
    public function update(StepRequest $request, League $league, Step $step): JsonResponse;

    /**
     * Delete the step.
     */
    public function destroy(Step $step): JsonResponse;

    /**
     * Store the club to the step.
     */
    public function storeClubs(StoreClubRequest $request, Step $step): JsonResponse;

    /**
     * Get the clubs of step.
     *
     * @return collectoin
     */
    public function getAllClubs(Step $step): Collection;

    /**
     * Get the matches of step.
     *
     * @return collectoin
     */
    public function getAllMatches(Step $step): Collection;
}
