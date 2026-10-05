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
interface IAdvertiseRepository
{
    /**
     * Get the advertises.
     */
    public function index(array $places): array;

    /**
     * Submit form of advertise.
     */
    public function advertiseForm(AdvertiseFormRequest $request): array;

    /**
     * Get the places.
     */
    public function getPlaces(): array;

    /**
     * Get the advertise pagination.
     */
    public function indexPaginate(TableRequest $request): LengthAwarePaginator;

    /**
     * Get the advertise pagination.
     */
    public function indexFormPaginate(TableRequest $request): LengthAwarePaginator;

    /**
     * Get the advertise info.
     */
    public function show(Advertise $advertise): Advertise;

    /**
     * Store the Advertise.
     */
    public function store(AdvertiseRequest $request): JsonResponse;

    /**
     * Update the advertise.
     *
     * @throws \Exception
     */
    public function update(AdvertiseUpdateRequest $request, Advertise $advertise): JsonResponse;

    /**
     * Delete the advertise.
     *
     * @param  UpdatePasswordRequest  $request
     */
    public function destroy(Advertise $advertise): JsonResponse;

    /**
     * Delete the advertise form.
     *
     * @param  UpdatePasswordRequest  $request
     */
    public function destroyForm(AdvertiseForm $advertiseForm): JsonResponse;
}
