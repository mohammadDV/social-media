<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\AdvertiseRequest;
use App\Http\Requests\AdvertiseUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Models\Advertise;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IAdvertiseRepository.
 */
interface IAdvertiseRepository  {

   /**
     * Get the places.
     * @param array %places
     * @return array
     */
    public function index(array $places) : array;

    /**
     * Get the advertise pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator;

    /**
     * Get the advertise info.
     * @param Advertise $advertise
     * @return Advertise
     */
    public function show(Advertise $advertise) :Advertise;

    /**
     * Store the Advertise.
     * @param AdvertiseRequest $request
     * @return JsonResponse
     */
    public function store(AdvertiseRequest $request) :JsonResponse;

    /**
     * Update the advertise.
     * @param AdvertiseUpdateRequest $request
     * @param Advertise $advertise
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(AdvertiseUpdateRequest $request, Advertise $advertise) :JsonResponse;

    /**
    * Delete the advertise.
    * @param UpdatePasswordRequest $request
    * @param Advertise $advertise
    * @return JsonResponse
    */
   public function destroy(Advertise $advertise) :JsonResponse;

}
