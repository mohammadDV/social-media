<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ILiveRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LiveController extends Controller
{
    /**
     * Constructor of ILiveRepository.
     */
    public function __construct(protected ILiveRepository $repository)
    {
        //
    }

    /**
     * Get all of lives.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->repository->index(), Response::HTTP_OK);
    }

}
