<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\MatchRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Matches;
use App\Models\Step;
use Illuminate\Http\JsonResponse;

/**
 * Interface IStepRepository.
 */
interface IMatchRepository
{
    /**
     * Get the match info.
     */
    public function show(Matches $matches): Matches;

    /**
     * Store the match.
     */
    public function store(MatchRequest $request, Step $step): JsonResponse;

    /**
     * Update the match.
     *
     * @throws \Exception
     */
    public function update(MatchRequest $request, Step $step, Matches $match): JsonResponse;

    /**
     * Delete the match.
     *
     * @param  UpdatePasswordRequest  $request
     */
    public function destroy(Matches $match): JsonResponse;
}
