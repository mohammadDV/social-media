<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\SendNotificationRequest;
use App\Http\Requests\TableRequest;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface INotificationRepository.
 */
interface INotificationRepository
{
    /**
     * Get the notification pagination.
     */
    public function indexPaginate(TableRequest $request): LengthAwarePaginator;

    /**
     * Get the notification info.
     */
    public function show(Notification $notification): Notification;

    /**
     * Delete the notification.
     *
     * @param  UpdatePasswordRequest  $request
     */
    public function destroy(Notification $notification): JsonResponse;

    /**
     * Send a notification.
     */
    public function sendAsAdmin(SendNotificationRequest $request): JsonResponse;

    /**
     * Check the notification users count
     *
     * @return JsonResponse
     */
    public function checkNotificationCount(SendNotificationRequest $request): array;

    /**
     * Get all notification sends.
     */
    public function sendListPaginate(TableRequest $request): LengthAwarePaginator;
}
