<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRequest;
use App\Http\Requests\CountryUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Models\Country;
use App\Repositories\Contracts\ICountryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CountryController extends Controller
{
    /**
     * Constructor of CountryController.
     */
    public function __construct(protected ICountryRepository $repository)
    {
        //
    }

    /**
     * Get all of Countries with pagination
     */
    public function indexPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get all of Countries
     */
    public function index(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->index($request), Response::HTTP_OK);
    }

    /**
     * Get the country.
     */
    public function show(Country $country): JsonResponse
    {
        return response()->json($this->repository->show($country), Response::HTTP_OK);
    }

    /**
     * Store the country.
     */
    public function store(CountryRequest $request): JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Update the country.
     */
    public function update(CountryUpdateRequest $request, Country $country): JsonResponse
    {
        return $this->repository->update($request, $country);
    }

    /**
     * Delete the country.
     */
    public function destroy(Country $country): JsonResponse
    {
        return $this->repository->destroy($country);
    }
}
