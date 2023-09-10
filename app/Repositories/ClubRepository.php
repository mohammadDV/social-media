<?php

namespace App\Repositories;

use App\Http\Requests\ClubRequest;
use App\Http\Requests\ClubUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Club;
use App\Repositories\Contracts\IClubRepository;
use App\Repositories\traits\GlobalFunc;
use App\Services\File\FileService;
use App\Services\Image\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ClubRepository implements IClubRepository {

    use GlobalFunc;

    /**
     * @param ImageService $imageService
     * @param FileService $fileService
     */
    public function __construct(protected ImageService $imageService, protected FileService $fileService)
    {

    }

    /**
     * Get the clubs pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator
    {
        $search = $request->input('search') ?? null;
        $count = $request->input('count') ?? 10;

        return Club::query()
            ->with('sport','country')
            ->orderBy($request->get('column') ?? 'id', $request->get('sort') ?? 'desc')
            ->when(!empty($search), function ($query) use($search) {
                $query->where('title','like','%' . $search . '%')
                ->orWhere('alias_title','like','%' . $search . '%');
            })
            ->paginate($count);
    }

    /**
     * Get the club.
     * @param Club $club
     * @return Club
     */
    public function show(Club $club) :Club
    {
        return Club::query()
                ->with('sport','country')
                ->where('id', $club->id)
                ->first();
    }

    /**
     * Store the club.
     * @param ClubRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(ClubRequest $request) :JsonResponse
    {
        $this->checkLevelAccess();

        $imageResult    = false;
        if ($request->hasFile('image')){
            $this->imageService->setExclusiveDirectory('uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'clubs');
            $imageResult = $this->imageService->save($request->file('image'));
        }

        if (!$imageResult){
            throw new \Exception("Image has a problem");
        }

        $club = Club::create([
            'alias_id'      => $request->input('alias_id'),
            'alias_title'   => $request->input('alias_title'),
            'title'         => $request->input('title'),
            'image'         => $imageResult,
            'country_id'    => $request->input('country_id'),
            'sport_id'      => $request->input('sport_id'),
            'user_id'       => Auth::user()->id,
            'status'        => $request->input('status'),
        ]);

        if ($club) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();
    }

    /**
     * Update the club.
     * @param ClubUpdateRequest $request
     * @param Club $club
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(ClubUpdateRequest $request, Club $club) :JsonResponse
    {
        $this->checkLevelAccess(Auth::user()->id == $club->user_id);

        $imageResult    = $club->image;
        if ($request->hasFile('image')){
            $this->imageService->setExclusiveDirectory('uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'clubs');
            $imageResult = $this->imageService->save($request->file('image'));
            if ($imageResult && !empty($club->image)){
                $this->imageService->deleteImage($club->image);
            }
        }
        if (!$imageResult){
            throw new \Exception("Image has a problem");
        }

        $club = $club->update([
            'alias_id'      => $request->input('alias_id'),
            'alias_title'   => $request->input('alias_title'),
            'title'         => $request->input('title'),
            'image'         => $imageResult,
            'country_id'    => $request->input('country_id'),
            'sport_id'      => $request->input('sport_id'),
            'user_id'       => auth()->user()->id,
            'status'        => $request->input('status'),
        ]);

        if ($club) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
    }

    /**
    * Delete the club.
    * @param UpdatePasswordRequest $request
    * @param Club $club
    * @return JsonResponse
    */
   public function destroy(Club $club) :JsonResponse
   {
        $this->checkLevelAccess(Auth::user()->id == $club->user_id);

        $club->delete();

        if ($club) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
   }
}
