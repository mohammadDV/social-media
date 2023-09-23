<?php

namespace App\Repositories;

use App\Http\Requests\AdvertiseRequest;
use App\Http\Requests\AdvertiseUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Models\Advertise;
use App\Repositories\Contracts\IAdvertiseRepository;
use App\Repositories\traits\GlobalFunc;
use App\Services\File\FileService;
use App\Services\Image\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class AdvertiseRepository implements IAdvertiseRepository {

    use GlobalFunc;

    /**
     * @param ImageService $imageService
     * @param FileService $fileService
     */
    public function __construct(protected ImageService $imageService, protected FileService $fileService)
    {

    }

    /**
     * Get the places.
     * @return array
     */
    public function getPlaces() : array {

        return [
            1   => __('site.Top main page'),
            2   => __('site.Left main page'),
            3   => __('site.Top ranking main page'),
            4   => __('site.Top archive page'),
            5   => __('site.Right archive page'),
            6   => __('site.Top single page'),
            7   => __('site.Right single page'),
            8   => __('site.Top static page'),
            9   => __('site.Top static page'),
            10  => __('site.Right static page'),
        ];
    }

    /**
     * Get the places.
     * @param array %places
     * @return array
     */
    public function index(array $places) : array {

        // ->addMinutes('1'),
        $advertise = cache()->remember("advertise.all", now(), function () use($places) {
            return Advertise::query()
                ->where('status', 1)
                ->whereIn('place_id',$places)
                ->get();
        });

        $result = [];

        foreach($advertise ?? [] as $key => $item) {
            $result[$item->place_id][$key]['id']         = $item->id;
            $result[$item->place_id][$key]['place_id']   = $item->place_id;
            $result[$item->place_id][$key]['title']      = $item->title;
            $result[$item->place_id][$key]['link']       = $item->link;
            $result[$item->place_id][$key]['status']     = $item->status;
            $result[$item->place_id][$key]['image']      = !empty($item->image) ? asset($item->image) : asset('/assets/site/images/user-icon.png');
        }

        return $result;
    }

    /**
     * Get the advertise pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator
    {
        $search = $request->input('search') ?? null;
        $count = $request->input('count') ?? 10;

        return Advertise::query()
            ->orderBy($request->get('column') ?? 'id', $request->get('sort') ?? 'desc')
            ->when(!empty($search), function ($query) use($search) {
                $query->where('title','like','%' . $search . '%');
            })
            ->paginate($count);
    }

    /**
     * Get the advertise info.
     * @param Advertise $advertise
     * @return Matches
     */
    public function show(Advertise $advertise) :Advertise
    {
        return $advertise;
    }

    /**
     * Store the Advertise.
     * @param AdvertiseRequest $request
     * @return JsonResponse
     */
    public function store(AdvertiseRequest $request) :JsonResponse
    {
        $this->checkLevelAccess();

        $imageService   = new ImageService();
        $imageResult    = null;
        if ($request->hasFile('image')){
            $imageService->setExclusiveDirectory('uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'advertise');
            $imageResult = $imageService->save($request->file('image'));
        }

        if (empty($imageResult)) {
            throw new \Exception("Error for uploading the file");
        }

        $advertise = Advertise::create([
            'title'         => $request->input('title'),
            'image'         => $imageResult,
            'place_id'      => $request->input('place_id'),
            'link'          => $request->input('link'),
            'user_id'       => Auth::user()->id,
            'status'        => $request->input('status'),
        ]);

        if ($advertise) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();

    }

    /**
     * Update the advertise.
     * @param AdvertiseRequest $request
     * @param Advertise $advertise
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(AdvertiseUpdateRequest $request, Advertise $advertise) :JsonResponse
    {
        $this->checkLevelAccess(Auth::user()->id == $advertise->user_id);

        $imageService   = new ImageService();
        $imageResult    = $advertise->image;
        if ($request->hasFile('image')){
            $imageService->setExclusiveDirectory('uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'advertise');
            $imageResult = $imageService->save($request->file('image'));
            if ($imageResult && !empty($advertise->image)){
                $imageService->deleteImage($advertise->image);
            }
        }

        $advertise->update([
            'title'         => $request->input('title'),
            'image'         => $imageResult,
            'place_id'      => $request->input('place_id'),
            'user_id'       => auth()->user()->id,
            'status'        => $request->input('status'),
            'link'          => $request->input('link'),
        ]);

        if ($advertise) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();

    }

    /**
    * Delete the advertise.
    * @param Advertise $advertise
    * @return JsonResponse
    */
   public function destroy(Advertise $advertise) :JsonResponse
   {
        $this->checkLevelAccess(Auth::user()->id == $advertise->user_id);

        $advertise->delete();

        if ($advertise) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
   }
}
