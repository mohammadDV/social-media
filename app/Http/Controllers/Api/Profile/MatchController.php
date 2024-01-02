<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\MatchRequest;
use App\Models\Matches;
use App\Models\Step;
use App\Repositories\Contracts\IMatchRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MatchController extends Controller
{
    /**
     * Constructor of MatchController.
     */
    public function __construct(protected  IMatchRepository $repository)
    {
        //
    }

    /**
     * Get the match.
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
     * Update the match.
     * @param MatchRequest $request
     * @param Step $step
     * @param Matches $matches
     * @return JsonResponse
     */
    public function update(MatchRequest $request, Step $step, Matches $matches) :JsonResponse
    {
        return $this->repository->update($request, $step, $matches);
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
