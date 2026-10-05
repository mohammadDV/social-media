<?php

namespace App\Repositories;

use App\Http\Requests\TableRequest;
use App\Http\Requests\VideoFormRequest;
use App\Models\Video;
use App\Repositories\Contracts\IVideoRepository;
use App\Repositories\traits\GlobalFunc;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VideoRepository implements IVideoRepository
{
    use GlobalFunc;

    /**
     * Get the video.
     *
     * @return array
     */
    public function show(Video $video)
    {

        $this->checkLevelAccess($video->user_id == Auth::user()->id);

        return $video;
    }

    /**
     * Get all active videos.
     */
    public function index(): Collection
    {

        return Video::query()
            ->when(Auth::user()->level != 3, function ($query) {
                return $query->where('user_id', Auth::user()->id);
            })
            ->where('status', 1)
            ->get();
    }

    /**
     * Get the video pagination.
     */
    public function indexPaginate(TableRequest $request): LengthAwarePaginator
    {

        $search = $request->get('query');

        return Video::query()
            ->when(Auth::user()->level != 3, function ($query) {
                return $query->where('user_id', Auth::user()->id);
            })
            ->when(! empty($search), function ($query) use ($search) {
                return $query->where('title', 'like', '%'.$search.'%');
            })
            ->orderBy($request->get('sortBy', 'id'), $request->get('sortType', 'desc'))
            ->paginate($request->get('rowsPerPage', 25));
    }

    /**
     * Store the video.
     *
     * @throws \Exception
     */
    public function store(VideoFormRequest $request): JsonResponse
    {

        auth()->user()->videos()->create([
            'title' => $request->input('title'),
            'file' => $request->input('file', null),
            // 'image'       => $request->input('image', null),
            // 'type'        => $request->input('type',0),
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'status' => 1,
            'message' => __('site.The operation has been successfully'),
        ], 200);
    }

    /**
     * Update the video.
     *
     * @throws \Exception
     */
    public function update(VideoFormRequest $request, Video $video): JsonResponse
    {

        $this->checkLevelAccess($video->user_id == Auth::user()->id);

        DB::beginTransaction();
        try {
            $video->update([
                'title' => $request->input('title'),
                // 'image'       => $request->input('image', null),
                'file' => $request->input('file'),
                'status' => $request->input('status'),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception(__('site.Error in save data'));
        }

        return response()->json([
            'status' => 1,
            'message' => __('site.The operation has been successfully'),
        ], 200);
    }

    /**
     * Delete the video.
     *
     * @throws \Exception
     */
    public function destroy(Video $video): JsonResponse
    {

        $this->checkLevelAccess($video->user_id == Auth::user()->id);

        $video->delete();

        return response()->json([
            'status' => 1,
            'message' => __('site.The operation has been successfully'),
        ], 200);
    }
}
