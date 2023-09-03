<?php

namespace App\Repositories;

use App\Models\Advertise;
use App\Models\Live;
use App\Repositories\Contracts\IAdvertiseRepository;
use App\Services\Image\ImageService;

class AdvertiseRepository implements IAdvertiseRepository {

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

    public function store($request) {

        $imageService   = new ImageService();
        $imageResult    = null;
        if ($request->hasFile('image')){
            $imageService->setExclusiveDirectory('uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'advertise');
            $imageResult = $imageService->save($request->file('image'));
        }

        return Advertise::create([
            'title'         => $request->input('title'),
            'image'         => $imageResult,
            'place_id'      => $request->input('place_id'),
            'link'          => $request->input('link'),
            'user_id'       => auth()->user()->id,
            'status'        => $request->input('status'),
        ]);

    }

    public function update($request,Advertise $advertise) {

        $imageService   = new ImageService();
        $imageResult    = $advertise->image;
        if ($request->hasFile('image')){
            $imageService->setExclusiveDirectory('uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'advertise');
            $imageResult = $imageService->save($request->file('image'));
            if ($imageResult && !empty($advertise->image)){
                $imageService->deleteImage($advertise->image);
            }
        }

        return $advertise->update([
            'title'         => $request->input('title'),
            'image'         => $imageResult,
            'place_id'      => $request->input('place_id'),
            'user_id'       => auth()->user()->id,
            'status'        => $request->input('status'),
            'link'          => $request->input('link'),
        ]);

    }

    public function get($request) : array {
        $draw               = $request->get('draw');
        $start              = $request->get('start');
        $rowperpage         = $request->get('length');
        $columnIndex_arr    = $request->get('order');
        $columnName_arr     = $request->get('columns');
        $order_arr          = $request->get('order');
        $search_arr         = $request->get('search');
        $columnIndex        = $columnIndex_arr[0]['column'];
        $columnName         = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder    = $order_arr[0]['dir'];
        $searchValue        = $search_arr['value'];
        $totalRecords       = Advertise::select('count(*) as allcount')->count();

        $totalRecordsWithFilter = Advertise::select('count(*) as allcount')
            ->where('title','like','%' . $searchValue . '%')
            ->count();

        $records = Advertise::orderBy($columnName,$columnSortOrder)
            ->where('title','like','%' . $searchValue . '%')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $places   = $this->getPlaces();

        $data_arr = [];
        foreach ($records as $record) {
            $data_arr[] = [
                'id'            => $record->id,
                'title'         => $record->title,
                'user_id'       => $record->user_id,
                'place'         => $places[$record->place_id],
                'status'        => $record->status_name,
                'created_at'    => $record->created_at->diffforhumans(),
            ];
        }

        return [
            "draw"                  => intval($draw),
            "iTotalRecords"         => $totalRecords,
            "iTotalDisplayRecords"  => $totalRecordsWithFilter,
            "aaData"                => $data_arr
        ];


    }
}
