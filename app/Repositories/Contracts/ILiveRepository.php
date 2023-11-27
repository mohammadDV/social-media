<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\LiveRequest;
use App\Http\Requests\TableRequest;
use App\Models\Live;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

 /**
 * Interface ILiveRepository.
 */
interface ILiveRepository  {

    /**
     * Get the lives.
     * @return array
     */
    public function index() :array;

    /**
     * Get the lives pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator;

    /**
     * Get the live.
     * @param Live $live
     * @return Live
     */
    public function show(Live $live) :Live;

    /**
     * Store the live.
     * @param LiveRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(LiveRequest $request) :JsonResponse;

    /**
     * Update the live.
     * @param LiveRequest $request
     * @param Live $live
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(LiveRequest $request, Live $live) :JsonResponse;

    /**
    * Delete the live.
    * @param UpdatePasswordRequest $request
    * @param Live $live
    * @return JsonResponse
    */
   public function destroy(Live $live) :JsonResponse;

}
