<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendNotificationRequest;
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
     */
    public function indexPaginate(TableRequest $request, ?User $user): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request, $user), Response::HTTP_OK);
    }

    /**
     * Get the notification.
     */
    public function show(Notification $notification): JsonResponse
    {

        return response()->json($this->repository->show($notification), Response::HTTP_OK);
    }

    /**
     * Send a notification.
     */
    public function sendAsAdmin(SendNotificationRequest $request): JsonResponse
    {

        return $this->repository->sendAsAdmin($request);
    }

    /**
     * List of notification send.
     */
    public function sendListPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->sendListPaginate($request), Response::HTTP_OK);
    }

    /**
     * Send a notification.
     */
    public function checkNotificationCount(SendNotificationRequest $request): JsonResponse
    {

        return response()->json($this->repository->checkNotificationCount($request), Response::HTTP_OK);
    }

    /**
     * Delete the notification.
     */
    public function destroy(Notification $notification): JsonResponse
    {
        return $this->repository->destroy($notification);
    }
}
