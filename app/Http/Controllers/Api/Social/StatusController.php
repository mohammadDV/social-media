<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Models\User;
use App\Repositories\Contracts\IStatusRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StatusController extends Controller
{
    /**
     * Constructor of StatusController.
     */
    public function __construct(protected IStatusRepository $repository)
    {
        //
    }

    /**
     * Get all of statuses
     * @param ?User $user
     * @return JsonResponse
     */
    public function index(?User $user): JsonResponse
    {
        return response()->json($this->repository->index($user), Response::HTTP_OK);
    }

    /**
     * Get the status info
     * @param ?User $user
     * @return JsonResponse
     */
    public function getInfo(Status $status): JsonResponse
    {
        return response()->json($this->repository->getInfo($status), Response::HTTP_OK);
    }
}
