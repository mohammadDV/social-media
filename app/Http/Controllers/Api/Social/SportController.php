<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ISportRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SportController extends Controller
{
    /**
     * Constructor of SportController.
     */
    public function __construct(protected ISportRepository $repository)
    {
        //
    }

    /**
     * Get all of sports
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->repository->index(), Response::HTTP_OK);
    }
}
