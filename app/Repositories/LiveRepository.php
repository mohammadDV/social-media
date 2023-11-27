<?php

namespace App\Repositories;

use App\Http\Requests\LiveRequest;
use App\Http\Requests\TableRequest;
use App\Models\Live;
use App\Repositories\Contracts\ILiveRepository;
use App\Repositories\traits\GlobalFunc;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LiveRepository implements ILiveRepository {


    use GlobalFunc;

    /**
     * Get the live.
     * @return array
     */
    public function index() :array
    {
        // ->addMinutes('1'),
        $livesRow = cache()->remember("live.all", now(),
            function () {
            return Live::query()->orderBy('priority','ASC')->take(50)->get();
        });

        $lives          = [];
        foreach($livesRow ?? [] as $live){
            $lives[slug($live->date)][] = [
                "title"     => $live->title,
                "teams"     => $live->teams,
                "hour"      => $live->hour,
                "date"      => $live->date,
                "link"      => $live->link,
                "info"      => $live->info,
                "priority"  => $live->priority,
            ];
        }
        return $lives;

    }

    /**
     * Get the lives pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator
    {
        $search = $request->get('query');
        return Live::query()
            ->when(Auth::user()->level != 3, function ($query) {
                return $query->where('user_id', Auth::user()->id);
            })
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('teams','like','%' . $search . '%')
                    ->orWhere('date','like','%' . $search . '%');
            })
            ->orderBy($request->get('sortBy', 'priority'), $request->get('sortType', 'asc'))
            ->paginate($request->get('rowsPerPage', 25));
    }

    /**
     * Get the live.
     * @param Live $live
     * @return Live
     */
    public function show(Live $live) :Live
    {
        return Live::query()
                ->where('id', $live->id)
                ->first();
    }

    /**
     * Store the live.
     * @param LiveRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(LiveRequest $request) :JsonResponse
    {
        $this->checkLevelAccess();

        $live = Live::create([
            'title'      => $request->input('title'),
            'teams'      => $request->input('teams'),
            'date'       => $request->input('date'),
            'hour'       => $request->input('hour'),
            'link'       => $request->input('link'),
            'info'       => $request->input('info'),
            'priority'   => $request->input('priority'),
            'user_id'    => Auth::user()->id,
            'status'     => $request->input('status'),
        ]);

        if ($live) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();
    }

    /**
     * Update the live.
     * @param LiveUpdateRequest $request
     * @param Live $live
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(LiveRequest $request, Live $live) :JsonResponse
    {
        $this->checkLevelAccess(Auth::user()->id == $live->user_id);

        $live = $live->update([
            'title'      => $request->input('title'),
            'teams'      => $request->input('teams'),
            'date'       => $request->input('date'),
            'hour'       => $request->input('hour'),
            'link'       => $request->input('link'),
            'info'       => $request->input('info'),
            'priority'   => $request->input('priority'),
            'user_id'    => Auth::user()->id,
            'status'     => $request->input('status'),
        ]);

        if ($live) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
    }

    /**
    * Delete the live.
    * @param UpdatePasswordRequest $request
    * @param Live $live
    * @return JsonResponse
    */
   public function destroy(Live $live) :JsonResponse
   {
        $this->checkLevelAccess(Auth::user()->id == $live->user_id);

        $live->delete();

        if ($live) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
   }
}
