<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\SendNotificationRequest;
use App\Http\Requests\TableRequest;
use App\Models\Notification;
use App\Models\User;
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
    public function indexPaginate(TableRequest $request, ?User $user = null): LengthAwarePaginator;

    /**
     * Get the notification info.
     */
    public function show(Notification $notification): Notification;

    /**
     * Delete the notification.
     */
    public function destroy(Notification $notification): JsonResponse;

    /**
     * Send a notification.
     */
    public function sendAsAdmin(SendNotificationRequest $request): JsonResponse;

    /**
     * Check the notification users count
     */
    public function checkNotificationCount(SendNotificationRequest $request): array;

    /**
     * Get all notification sends.
     */
    public function sendListPaginate(TableRequest $request): LengthAwarePaginator;
}
