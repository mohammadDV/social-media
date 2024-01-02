<?php

namespace App\Repositories;

use App\Http\Requests\MatchRequest;
use App\Http\Requests\StoreClubRequest;
use App\Http\Requests\StepRequest;
use App\Models\ClubStep;
use App\Services\Image\ImageService;
use App\Services\MatchService;
use App\Models\League;
use App\Models\Matches;
use App\Models\Step;
use App\Repositories\Contracts\IMatchRepository;
use App\Repositories\traits\GlobalFunc;
use App\Services\File\FileService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MatchRepository extends MatchService implements IMatchRepository {

    use GlobalFunc;

    /**
     * @param ImageService $imageService
     * @param FileService $fileService
     */
    public function __construct(protected ImageService $imageService, protected FileService $fileService)
    {

    }

    /**
     * Get the match info.
     * @param Matches $match
     * @return Matches
     */
    public function show(Matches $matches) :Matches
    {
        return Matches::query()
            ->where('id', $matches->id)
            ->with('teamHome', 'teamAway')
            ->first();
    }

    /**
     * Store the match.
     * @param MatchRequest $request
     * @param Step $step
     * @return JsonResponse
     */
    public function store(MatchRequest $request, Step $step) :JsonResponse
    {
        $this->checkLevelAccess();

        $match = Matches::create([
            "home_id"   => $request->home_id,
            "away_id"   => $request->away_id,
            "hsc"       => $request->hsc,
            "asc"       => $request->asc,
            "link"      => $request->link,
            "status"    => 1,
            "date"      => $request->date,
            "hour"      => $request->hour,
            "priority"  => $request->priority,
            "user_id"   => Auth::user()->id,
            "step_id"   => $step->id,
        ]);

        if ($match) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();

    }

    /**
     * Update the match.
     * @param MatchRequest $request
     * @param Step $step
     * @param Matches $match
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(MatchRequest $request, Step $step, Matches $match) :JsonResponse
    {
        $this->checkLevelAccess(Auth::user()->id == $match->user_id);

        $match = Matches::find($match->id)->update([
            "home_id"   => $request->home_id,
            "away_id"   => $request->away_id,
            "hsc"       => $request->hsc,
            "asc"       => $request->asc,
            "link"      => $request->link,
            "status"    => 1,
            "date"      => $request->date,
            "hour"      => $request->hour,
            "priority"  => $request->priority,
            "user_id"   => Auth::user()->id,
        ]);

        if ($match) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();

    }

    /**
    * Delete the match.
    * @param Matches $match
    * @return JsonResponse
    */
   public function destroy(Matches $match) :JsonResponse
   {
        $this->checkLevelAccess(Auth::user()->id == $match->user_id);

        $match->delete();

        if ($match) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
   }
}
