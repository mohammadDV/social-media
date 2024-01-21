<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationRequest;
use App\Http\Requests\NotificationUpdateRequest;
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
    public function __construct(protected  INotificationRepository $repository)
    {
        //
    }

    /**
     * Get all of notification with pagination
     * @param TableRequest $request
     * @param ?User $user
     * @return JsonResponse
     */
    public function indexPaginate(TableRequest $request, ?User $user): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request, $user), Response::HTTP_OK);
    }

    /**
     * Get the notification.
     * @param Notification $notification
     * @return JsonResponse
     */
    public function show(Notification $notification) :JsonResponse
    {

        return response()->json($this->repository->show($notification), Response::HTTP_OK);
    }

    /**
     * Delete the notification.
     * @param Notification $notification
     * @return JsonResponse
     */
    public function destroy(Notification $notification) :JsonResponse
    {
        return $this->repository->destroy($notification);
    }
}
