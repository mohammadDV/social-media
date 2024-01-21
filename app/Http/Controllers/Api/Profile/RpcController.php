<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IRpcRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RpcController extends Controller
{
    /**
     * Constructor of NotificationController.
     */
    public function __construct(protected  IRpcRepository $repository)
    {
        //
    }

    /**
     * Get the necessary thing for user.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->repository->index(), Response::HTTP_OK);
    }
}
