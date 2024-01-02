<?php

namespace App\Repositories;

use App\Http\Requests\StoreClubRequest;
use App\Http\Requests\StepRequest;
use App\Models\ClubStep;
use App\Services\Image\ImageService;
use App\Services\MatchService;
use App\Models\League;
use App\Models\Step;
use App\Repositories\Contracts\IStepRepository;
use App\Repositories\traits\GlobalFunc;
use App\Services\File\FileService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StepRepository extends MatchService implements IStepRepository {

    use GlobalFunc;

    /**
     * @param ImageService $imageService
     * @param FileService $fileService
     */
    public function __construct(protected ImageService $imageService, protected FileService $fileService)
    {

    }

    /**
     * Get the step.
     * @param Step $step
     * @return Step
     */
    public function show(Step $step) :Step
    {
        return Step::query()
            ->where('id', $step->id)
            ->with('league')->first();
    }

    /**
     * Get the step info.
     * @param Step $step
     * @return array
     */
    public function getStepInfo(Step $step) :array
    {
        $step = Step::query()
            ->where('id', $step->id)
            ->with('league')->first();
            // ->with('league', 'matches', 'matches.teamHome', 'matches.teamAway', 'clubs')->first();

        $data['matches']    = $this->getMatches($step->id);

        // dd();
        if($step->league->type == 1){
            $data['clubs']      = $this->getClubs($step);
        }else{
            $data['clubs']      = $this->getTournamentClubs($step->id);
        }

        return $data;

    }

    /**
    * Delete the step.
    * @param UpdatePasswordRequest $request
    * @param Step $step
    * @return JsonResponse
    */
   public function destroy(Step $step) :JsonResponse
   {
        $this->checkLevelAccess(Auth::user()->id == $step->user_id);

        $step->clubs()->delete();
        $step->matches()->delete();
        $step->delete();

        if ($step) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
   }

    /**
     * Store the step.
     * @param StepRequest $request
     * @param League $league
     * @return JsonResponse
     */
    public function store(StepRequest $request, League $league) :JsonResponse
    {
        $this->checkLevelAccess();

        if($request->current == 1) {
            Step::query()
                ->where('league_id', $league->id)
                ->where('current', 1)
                ->update([
                   'current' => 0
                ]);
        } else {
            if (Step::query()
            ->where('league_id', $league->id)
            ->count() == 0) {
                return response()->json([
                    'status' => 1,
                    'message' => __('site.You should have an active step')
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        $step = Step::create([
            "title"     => $request->title,
            "current"   => $request->current,
            "priority"  => $request->priority,
            "status"  => $request->status,
            "user_id"   => Auth::user()->id,
            "league_id" => $league->id,
        ]);

        if ($step) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();

    }

    /**
     * Update the step.
     * @param StepRequest $request
     * @param League $league
     * @param Step $step
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(StepRequest $request, League $league, Step $step) :JsonResponse
    {
        $this->checkLevelAccess(Auth::user()->id == $step->user_id);

        if($request->current == 1) {
            Step::query()
                ->where('league_id', $league->id)
                ->update([
                   'current' => 0
                ]);
        } else {
            if (Step::query()
            ->whereNot('id', $step->id)
            ->where('league_id', $league->id)
            ->where('current', 1)
            ->count() == 0) {
                return response()->json([
                    'status' => 1,
                    'message' => __('site.You should have an active step')
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        $step->update([
            "title"     => $request->title,
            "current"   => $request->current,
            "priority"  => $request->priority,
            "status"    => $request->status,
            "user_id"   => Auth::user()->id,
        ]);

        if ($step) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();

    }

    /**
    * Get the clubs of step.
    * @param Step $step
    * @return collectoin
    */
   public function getClubs(Step $step) :Collection
   {
        return League::find($step->league->id)->clubs;
   }

    /**
    * Get the clubs of step.
    * @param Step $step
    * @return collectoin
    */
   public function getAllClubs(Step $step) :Collection
   {
        return Step::find($step->id)->clubs;
   }

    /**
    * Get the matches of step.
    * @param Step $step
    * @return collectoin
    */
   public function getAllMatches(Step $step) :Collection
   {
        return Step::find($step->id)->matches;
   }

    /**
    * Store the club to the league.
    * @param StoreClubRequest $request
    * @param League $league
    * @return JsonResponse
    */
    public function storeClubs(StoreClubRequest $request, Step $step) :JsonResponse
    {
        $step->clubs()->sync($request->all());

        return response()->json([
            'status' => 1,
            'message' => __('site.Clubs has been stored')
        ], Response::HTTP_OK);
    }
}
