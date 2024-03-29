<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdvertiseFormRequest;
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
     * Get all of post except newspaper.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->repository->index(range(1,7)), Response::HTTP_OK);
    }

    /**
     * Submit form of advertise.
     * @param AdvertiseFormRequest $request
     *
     * @return JsonResponse
     */
    public function advertiseForm(AdvertiseFormRequest $request): JsonResponse
    {
        return response()->json($this->repository->advertiseForm($request), Response::HTTP_OK);
    }
}
