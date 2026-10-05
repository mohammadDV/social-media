<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\TableRequest;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\INotificationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    /**
     * Constructor of NotificationController.
     */
    public function __construct(protected INotificationRepository $repository)
    {
        //
    }

    /**
     * Get all of notification with pagination
     *
     * @param  ?User  $user
     */
    public function indexPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }
}
