<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\TableRequest;
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
     */
    public function index(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->index($request), Response::HTTP_OK);
    }
}
