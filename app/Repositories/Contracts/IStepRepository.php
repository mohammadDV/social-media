<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\StepRequest;
use App\Http\Requests\StepUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\StoreClubRequest;
use App\Models\League;
use App\Models\Step;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IStepRepository.
 */
interface IStepRepository  {

    /**
     * Get the step info.
     * @param Step $step
     * @return array
     */
    public function getStepInfo(Step $step) :array;

    /**
     * Store the step.
     * @param StepRequest $request
     * @param League $league
     * @return JsonResponse
     */
    public function store(StepRequest $request, League $league) :JsonResponse;

    /**
     * Update the step.
     * @param StepRequest $request
     * @param League $league
     * @param Step $step
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(StepRequest $request, League $league, Step $step) :JsonResponse;

    /**
    * Delete the step.
    * @param Step $step
    * @return JsonResponse
    */
   public function destroy(Step $step) :JsonResponse;

    /**
    * Store the club to the step.
    * @param StoreClubRequest $request
    * @param Step $step
    * @return JsonResponse
    */
   public function storeClubs(StoreClubRequest $request, Step $step) :JsonResponse;

    /**
    * Get the clubs of step.
    * @param Step $step
    * @return collectoin
    */
//    public function getClubs(Step $step) :Collection;

}
