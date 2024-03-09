<?php

namespace App\Repositories;

use App\Http\Requests\StatusRequest;
use App\Http\Requests\StatusUpdateRequest;
use App\Models\Favorite;
use App\Models\Status;
use App\Models\User;
use App\Repositories\Contracts\IStatusRepository;
use App\Repositories\traits\GlobalFunc;
use App\Services\File\FileService;
use App\Services\Image\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatusRepository implements IStatusRepository {


    use GlobalFunc;

    /**
     * @param ImageService $imageService
     * @param FileService $fileService
     */
    public function __construct(protected ImageService $imageService, protected FileService $fileService)
    {

    }

    /**
     * Get the status.
     * @param ?User $user
     * @return LengthAwarePaginator
     */
    public function index(?User $user) :LengthAwarePaginator
    {
        // ->addMinutes('1'),
        // return cache()->remember("status.all" . !empty($user) ? $user?->id : '', now(),
        return Status::query()
            ->when(!empty($user->id), function ($query) use($user) {
                return $query->where('user_id', $user->id);
            })
            ->with(['likes', 'user', 'favorites'])
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->paginate(10);
    }

    /**
     * Get the status.
     * @param ?User $user
     * @return LengthAwarePaginator
     */
    public function getAllPerUser(User $user) :LengthAwarePaginator
    {
        return Status::query()
            ->where('user_id', $user->id)
            ->with(['likes', 'user', 'favorites'])
            ->orderBy('id', 'DESC')
            ->paginate(2);
    }

    /**
     * Get favorites.
     * @param ?User $user
     * @return LengthAwarePaginator
     */
    public function getFavorite(User $user) :LengthAwarePaginator
    {
        return Status::query()
            ->whereHas('favorites', function($query) use($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['likes', 'user', 'favorites'])
            ->orderBy('id', 'DESC')
            ->paginate(2);
    }

    /**
     * Add the status to favorites.
     * @param Status $status
     * @return JsonResponse
     */
    public function addFavorite(Status $status) :JsonResponse
    {

        // if (empty($status->status)) {
        //     throw new \Exception();
        // }

        $query = Favorite::query()
            ->where('user_id', Auth::user()->id)
            ->where('favoritable_id', $status->id)
            ->where('favoritable_type', Status::class);

        if ($query->count() > 0) {
            $query->delete();

            return response()->json([
                'status' => 1,
                'active' => 0,
                'message' => __('site.The operation has been successfully')
            ], 200);
        }

        Favorite::create([
            'user_id' => Auth::user()->id,
            'favoritable_id' => $status->id,
            'favoritable_type' => Status::class,
        ]);

        return response()->json([
            'status' => 1,
            'active' => 1,
            'message' => __('site.The operation has been successfully')
        ], 200);
    }

     /**
     * Get the status info.
     * @param Status $status
     * @return StatusResource
     */
    public function getInfo(Status $status)
    {
        return Status::query()
            ->with(['likes','user'])
            ->where('id', $status->id)
            ->where('status', 1)
            ->first();
    }


    /**
     * Get all status.
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function statusPaginate(Request $request) :LengthAwarePaginator
    {

        $search = $request->get('query');
        return Status::query()
            ->when(Auth::user()->level != 3, function ($query) {
                return $query->where('user_id', Auth::user()->id);
            })
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('text', 'like', '%' . $search . '%');
            })
            ->withCount('comments')
            ->orderBy($request->get('sortBy', 'id'), $request->get('sortType', 'desc'))
            ->paginate($request->get('rowsPerPage', 25));

    }

    /**
     * Store the status.
     *
     * @param  StatusRequest  $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(StatusRequest $request) :JsonResponse
    {

        $imageResult = $request->get('file');

        Auth::user()->statuses()->create([
            'text'        => $request->input('text'),
            'file'        => $imageResult ?? null,
            'status'      => $request->input('status',0)
        ]);

        return response()->json([
            'status' => 1,
            'message' => __('site.New status has been stored')
        ], 200);
    }

    /**
     * Update the status.
     *
     * @param  StatusUpdateRequest  $request
     * @param  Status  $status
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(StatusUpdateRequest $request, Status $status) :JsonResponse
    {

        $this->checkLevelAccess($status->user_id == Auth::user()->id);

        $imageResult = $request->get('file');

        DB::beginTransaction();
        try {
            $status->update([
                'text'       => $request->input('text'),
                'file'        => $imageResult ?? null,
                'status'      => $request->input('status'),
            ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e);
        }

        return response()->json([
            'status' => 1,
            'message' => __('site.The status has been updated')
        ], 200);
    }

    /**
     * Delete the status.
     *
     * @param  Status  $status
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(Status $status) :JsonResponse
    {

        $this->checkLevelAccess($status->user_id == Auth::user()->id);

        $status->delete();

        return response()->json([
            'status' => 1,
            'message' => __('site.The status has been deleted')
        ], 200);
    }

    /**
     * Delete completely the status.
     * @param int $id
     * @return JsonResponse
     */
    public function realDestroy(int $id): JsonResponse
    {

        $this->checkLevelAccess();

        try {
            DB::beginTransaction();

            $status = Status::withTrashed()->where('id', $id)->first();

            if (!empty($status->file)){
                $this->fileService->deleteFile($status->file);
            }

            $delete = $status->forceDelete();
            if ($delete){
                DB::commit();
                return response()->json([
                    'status' => 1,
                    'message' => __('site.The status has been deleted')
                ], 200);
            }
        }catch (\Exception $e){
            DB::rollBack();
            throw new \Exception(__('site.Error in save data'));
        }

        throw new \Exception(__('site.Error in save data'));
    }
}
