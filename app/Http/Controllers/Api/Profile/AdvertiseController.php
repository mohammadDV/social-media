<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdvertiseRequest;
use App\Http\Requests\AdvertiseUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Models\Advertise;
use App\Models\AdvertiseForm;
use App\Repositories\Contracts\IAdvertiseRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AdvertiseController extends Controller
{
    /**
     * Constructor of AdvertiseController.
     */
    public function __construct(protected IAdvertiseRepository $repository)
    {
        //
    }

    /**
     * Get all places of advertises.
     */
    public function getPlaces(): JsonResponse
    {
        return response()->json($this->repository->getPlaces(), Response::HTTP_OK);
    }

    /**
     * Get all of advertise with pagination
     */
    public function indexPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get all of advertise form with pagination
     */
    public function indexFormPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexFormPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get the advertise.
     */
    public function show(Advertise $advertise): JsonResponse
    {

        return response()->json($this->repository->show($advertise), Response::HTTP_OK);
    }

    /**
     * Store the advertise.
     */
    public function store(AdvertiseRequest $request): JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Update the advertise.
     */
    public function update(AdvertiseUpdateRequest $request, Advertise $advertise): JsonResponse
    {
        return $this->repository->update($request, $advertise);
    }

    /**
     * Delete the advertise.
     */
    public function destroy(Advertise $advertise): JsonResponse
    {
        return $this->repository->destroy($advertise);
    }

    /**
     * Delete the advertise form.
     */
    public function destroyForm(AdvertiseForm $advertiseForm): JsonResponse
    {
        return $this->repository->destroyForm($advertiseForm);
    }
}
