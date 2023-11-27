<?php

namespace App\Repositories;

use App\Http\Requests\CountryRequest;
use App\Http\Requests\CountryUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Country;
use App\Repositories\Contracts\ICountryRepository;
use App\Repositories\traits\GlobalFunc;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class CountryRepository implements ICountryRepository {

    use GlobalFunc;

    /**
     * Get the countrys pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator
    {
        $search = $request->get('query');
        return Country::query()
            ->when(Auth::user()->level != 3, function ($query) {
                return $query->where('user_id', Auth::user()->id);
            })
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('alias_title','like','%' . $search . '%');
            })
            ->orderBy($request->get('sortBy', 'id'), $request->get('sortType', 'desc'))
            ->paginate($request->get('rowsPerPage', 25));
    }

    /**
     * Get the countrys.
     * @return Collection
     */
    public function index() :Collection
    {
        return Country::all();
    }

    /**
     * Get the country.
     * @param Country $country
     * @return Country
     */
    public function show(Country $country) :Country
    {
        return Country::query()
                ->where('id', $country->id)
                ->first();
    }

    /**
     * Store the country.
     * @param CountryRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(CountryRequest $request) :JsonResponse
    {
        $this->checkLevelAccess();

        $country = Country::create([
            'alias_id'      => $request->input('alias_id'),
            'alias_title'   => $request->input('alias_title'),
            'title'         => $request->input('title'),
            'image'         => $request->input('image'),
            'user_id'       => Auth::user()->id,
            'status'        => $request->input('status'),
        ]);

        if ($country) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();
    }

    /**
     * Update the country.
     * @param CountryUpdateRequest $request
     * @param Country $country
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(CountryUpdateRequest $request, Country $country) :JsonResponse
    {
        $this->checkLevelAccess(Auth::user()->id == $country->user_id);

        $country = $country->update([
            'alias_id'      => $request->input('alias_id'),
            'alias_title'   => $request->input('alias_title'),
            'title'         => $request->input('title'),
            'image'         => $request->input('image'),
            'user_id'       => auth()->user()->id,
            'status'        => $request->input('status'),
        ]);

        if ($country) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
    }

    /**
    * Delete the country.
    * @param UpdatePasswordRequest $request
    * @param Country $country
    * @return JsonResponse
    */
   public function destroy(Country $country) :JsonResponse
   {
        $this->checkLevelAccess(Auth::user()->id == $country->user_id);

        $country->delete();

        if ($country) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
   }
}
