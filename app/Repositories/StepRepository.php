<?php

namespace App\Repositories;

use App\Http\Requests\StepRequest;
use App\Http\Requests\StoreClubRequest;
use App\Models\League;
use App\Models\Step;
use App\Repositories\Contracts\IStepRepository;
use App\Repositories\traits\GlobalFunc;
use App\Services\File\FileService;
use App\Services\Image\ImageService;
use App\Services\MatchService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class StepRepository extends MatchService implements IStepRepository
{
    use GlobalFunc;

    public function __construct(protected ImageService $imageService, protected FileService $fileService) {}

    /**
     * Get the step.
     */
    public function show(Step $step): Step
    {
        return Step::query()
            ->where('id', $step->id)
            ->with('league')->first();
    }

    /**
     * Get the step info.
     */
    public function getStepInfo(Step $step): array
    {
        $step = Step::query()
            ->where('id', $step->id)
            ->with('league')->first();
        // ->with('league', 'matches', 'matches.teamHome', 'matches.teamAway', 'clubs')->first();

        $data['matches'] = $this->getMatches($step->id);

        // dd();
        $league = $step->league;
        if ($league instanceof League && $league->type == 1) {
            $data['clubs'] = $this->getClubs($step);
        } else {
            $data['clubs'] = $this->getTournamentClubs($step->id);
        }

        return $data;

    }

    /**
     * Delete the step.
     */
    public function destroy(Step $step): JsonResponse
    {
        $this->checkLevelAccess(Auth::user()->id == $step->user_id);

        $step->clubs()->delete();
        $step->matches()->delete();
        $step->delete();

        return response()->json([
            'status' => 1,
            'message' => __('site.The operation has been successfully'),
        ], Response::HTTP_OK);
    }

    /**
     * Store the step.
     */
    public function store(StepRequest $request, League $league): JsonResponse
    {
        $this->checkLevelAccess();

        if ($request->current == 1) {
            Step::query()
                ->where('league_id', $league->id)
                ->where('current', 1)
                ->update([
                    'current' => 0,
                ]);
        } else {
            if (Step::query()
                ->where('league_id', $league->id)
                ->count() == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => __('site.You should have an active step'),
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        $step = Step::create([
            'title' => $request->title,
            'current' => $request->current,
            'priority' => $request->priority,
            'status' => $request->status,
            'user_id' => Auth::user()->id,
            'league_id' => $league->id,
        ]);

        if ($step) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully'),
            ], Response::HTTP_CREATED);
        }

        throw new \Exception;
    }

    /**
     * Update the step.
     *
     * @throws \Exception
     */
    public function update(StepRequest $request, League $league, Step $step): JsonResponse
    {
        $this->checkLevelAccess(Auth::user()->id == $step->user_id);

        if ($request->current == 1) {
            Step::query()
                ->where('league_id', $league->id)
                ->update([
                    'current' => 0,
                ]);
        } else {
            if (Step::query()
                ->whereNot('id', $step->id)
                ->where('league_id', $league->id)
                ->where('current', 1)
                ->count() == 0) {
                return response()->json([
                    'status' => 0,
                    'message' => __('site.You should have an active step'),
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        $step->update([
            'title' => $request->title,
            'current' => $request->current,
            'priority' => $request->priority,
            'status' => $request->status,
            'user_id' => Auth::user()->id,
        ]);

        return response()->json([
            'status' => 1,
            'message' => __('site.The operation has been successfully'),
        ], Response::HTTP_OK);
    }

    /**
     * Get the clubs of step.
     */
    public function getClubs(Step $step): Collection
    {
        $league = $step->league;
        if (! $league instanceof League) {
            return new Collection;
        }

        $foundLeague = League::query()->find($league->id);

        return $foundLeague instanceof League ? $foundLeague->clubs : new Collection;
    }

    /**
     * Get the clubs of step.
     */
    public function getAllClubs(Step $step): Collection
    {
        return Step::find($step->id)->clubs;
    }

    /**
     * Get the matches of step.
     */
    public function getAllMatches(Step $step): Collection
    {
        return Step::find($step->id)->matches;
    }

    /**
     * Store the club to the league.
     */
    public function storeClubs(StoreClubRequest $request, Step $step): JsonResponse
    {
        $step->clubs()->sync($request->all());

        return response()->json([
            'status' => 1,
            'message' => __('site.Clubs has been stored'),
        ], Response::HTTP_OK);
    }
}
