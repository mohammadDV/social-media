<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\CountryRequest;
use App\Http\Requests\CountryUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Country;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface ICountryRepository.
 */
interface ICountryRepository  {

    /**
     * Get the countries pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator;

    /**
     * Get the countries.
     * @return Collection
     */
    public function index() :Collection;

    /**
     * Get the country.
     * @param Country $country
     * @return Country
     */
    public function show(Country $country) :Country;

    /**
     * Store the country.
     * @param CountryRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(CountryRequest $request) :JsonResponse;

    /**
     * Update the country.
     * @param CountryUpdateRequest $request
     * @param Country $country
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(CountryUpdateRequest $request, Country $country) :JsonResponse;

    /**
    * Delete the country.
    * @param UpdatePasswordRequest $request
    * @param Country $country
    * @return JsonResponse
    */
   public function destroy(Country $country) :JsonResponse;

}
