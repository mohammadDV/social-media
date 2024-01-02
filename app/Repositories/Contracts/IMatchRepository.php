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
interface IMatchRepository  {

    /**
     * Get the match info.
     * @param Matches $matches
     * @return Matches
     */
    public function show(Matches $matches) :Matches;

    /**
     * Store the match.
     * @param MatchRequest $request
     * @param Step $step
     * @return JsonResponse
     */
    public function store(MatchRequest $request, Step $step) :JsonResponse;

    /**
     * Update the match.
     * @param MatchRequest $request
     * @param Step $step
     * @param Matches $match
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(MatchRequest $request,Step $step, Matches $match) :JsonResponse;

    /**
    * Delete the match.
    * @param UpdatePasswordRequest $request
    * @param Matches $match
    * @return JsonResponse
    */
   public function destroy(Matches $match) :JsonResponse;

}
