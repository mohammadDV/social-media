<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\AdvertiseFormRequest;
use App\Http\Requests\AdvertiseRequest;
use App\Http\Requests\AdvertiseUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Models\Advertise;
use App\Models\AdvertiseForm;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IAdvertiseRepository.
 */
interface IAdvertiseRepository  {

   /**
     * Get the advertises.
     * @param array $places
     * @return array
     */
    public function index(array $places) : array;

   /**
     * Submit form of advertise.
     * @param AdvertiseFormRequest $request
     */
    public function advertiseForm(AdvertiseFormRequest $request) : array;

   /**
     * Get the places.
     * @return array
     */
    public function getPlaces() : array;

    /**
     * Get the advertise pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator;

    /**
     * Get the advertise pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexFormPaginate(TableRequest $request) :LengthAwarePaginator;

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

    /**
    * Delete the advertise form.
    * @param UpdatePasswordRequest $request
    * @param AdvertiseForm $advertiseForm
    * @return JsonResponse
    */
   public function destroyForm(AdvertiseForm $advertiseForm) :JsonResponse;

}
