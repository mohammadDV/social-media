<?php

namespace App\Repositories;

use App\Http\Requests\SportRequest;
use App\Http\Requests\SportUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Sport;
use App\Repositories\Contracts\ISportRepository;
use App\Repositories\traits\GlobalFunc;
use App\Services\File\FileService;
use App\Services\Image\ImageService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class SportRepository implements ISportRepository {

    use GlobalFunc;

    /**
     * @param ImageService $imageService
     * @param FileService $fileService
     */
    public function __construct(protected ImageService $imageService, protected FileService $fileService)
    {

    }

    /**
     * Get the sports pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator
    {
        $search = $request->get('query');
        return Sport::query()
            ->when(Auth::user()->level != 3, function ($query) {
                return $query->where('user_id', Auth::user()->id);
            })
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('alias_title','like','%' . $search . '%');
            })
            ->orderBy($request->get('sortBy', 'id'), $request->get('sortType', 'desc'))
            ->paginate($request->get('rowsPerPage', 25));
    }

    /**
     * Get the sports.
     * @return Collection
     */
    public function index() :Collection
    {
        return Sport::all();
    }

    /**
     * Get the sport.
     * @param Sport $sport
     * @return Sport
     */
    public function show(Sport $sport) :Sport
    {
        return Sport::query()
                ->where('id', $sport->id)
                ->first();
    }

    /**
     * Store the sport.
     * @param SportRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(SportRequest $request) :JsonResponse
    {
        $this->checkLevelAccess();

        $sport = Sport::create([
            'alias_id'      => $request->input('alias_id'),
            'alias_title'   => $request->input('alias_title'),
            'title'         => $request->input('title'),
            'image'         => $request->input('image'),
            'user_id'       => Auth::user()->id,
            'status'        => $request->input('status'),
        ]);

        if ($sport) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();
    }

    /**
     * Update the sport.
     * @param SportUpdateRequest $request
     * @param Sport $sport
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(SportUpdateRequest $request, Sport $sport) :JsonResponse
    {
        $this->checkLevelAccess(Auth::user()->id == $sport->user_id);

        $sport = $sport->update([
            'alias_id'      => $request->input('alias_id'),
            'alias_title'   => $request->input('alias_title'),
            'title'         => $request->input('title'),
            'image'         => $request->input('image'),
            'user_id'       => auth()->user()->id,
            'status'        => $request->input('status'),
        ]);

        if ($sport) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
    }

    /**
    * Delete the sport.
    * @param UpdatePasswordRequest $request
    * @param Sport $sport
    * @return JsonResponse
    */
   public function destroy(Sport $sport) :JsonResponse
   {
        $this->checkLevelAccess(Auth::user()->id == $sport->user_id);

        $sport->delete();

        if ($sport) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
   }
}
