<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\SportRequest;
use App\Http\Requests\SportUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Sport;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface ISportRepository.
 */
interface ISportRepository  {

    /**
     * Get the sports pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator;

    /**
     * Get the sports pagination.
     * @return Collection
     */
    public function index() :Collection;

    /**
     * Get the sport.
     * @param Sport $sport
     * @return Sport
     */
    public function show(Sport $sport) :Sport;

    /**
     * Store the sport.
     * @param SportRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(SportRequest $request) :JsonResponse;

    /**
     * Update the sport.
     * @param SportUpdateRequest $request
     * @param Sport $sport
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(SportUpdateRequest $request, Sport $sport) :JsonResponse;

    /**
    * Delete the sport.
    * @param Sport $sport
    * @return JsonResponse
    */
   public function destroy(Sport $sport) :JsonResponse;

}
