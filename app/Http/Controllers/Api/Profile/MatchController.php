<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\MatchRequest;
use App\Http\Requests\StepRequest;
use App\Http\Requests\StoreClubRequest;
use App\Models\League;
use App\Models\Matches;
use App\Models\Step;
use App\Repositories\Contracts\IMatchRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MatchController extends Controller
{
    /**
     * Constructor of StepController.
     */
    public function __construct(protected  IMatchRepository $repository)
    {
        //
    }

    /**
     * Get the step.
     * @param Matches $match
     * @return JsonResponse
     */
    public function show(Matches $matches) :JsonResponse
    {

        return response()->json($this->repository->show($matches), Response::HTTP_OK);
    }

    /**
     * Store the match.
     * @param MatchRequest $request
     * @param Step $step
     * @return JsonResponse
     */
    public function store(MatchRequest $request, Step $step) :JsonResponse
    {
        return $this->repository->store($request, $step);
    }

    /**
     * Update the step.
     * @param MatchRequest $request
     * @param Matches $matches
     * @return JsonResponse
     */
    public function update(MatchRequest $request, Matches $matches) :JsonResponse
    {
        return $this->repository->update($request, $matches);
    }

    /**
     * Delete the match.
     * @param Matches $matches
     * @return JsonResponse
     */
    public function destroy(Matches $matches) :JsonResponse
    {
        return $this->repository->destroy($matches);
    }
}
