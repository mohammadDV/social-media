<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
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
     * Get all of countries
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->repository->index(), Response::HTTP_OK);
    }
}
