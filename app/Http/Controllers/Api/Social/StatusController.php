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
     */
    public function index(?User $user): JsonResponse
    {
        return response()->json($this->repository->index($user), Response::HTTP_OK);
    }

    /**
     * Get all of statuses
     */
    public function getAllPerUser(User $user): JsonResponse
    {
        return response()->json($this->repository->getAllPerUser($user), Response::HTTP_OK);
    }

    /**
     * Get favorite of statuses
     */
    public function getFavorite(User $user): JsonResponse
    {
        return response()->json($this->repository->getFavorite($user), Response::HTTP_OK);
    }

    /**
     * Add status to favorites
     */
    public function addFavorite(Status $status): JsonResponse
    {
        return $this->repository->addFavorite($status);
    }

    /**
     * Get the status info
     *
     * @param  ?User  $user
     */
    public function getInfo(Status $status): JsonResponse
    {
        return response()->json($this->repository->getInfo($status), Response::HTTP_OK);
    }
}
