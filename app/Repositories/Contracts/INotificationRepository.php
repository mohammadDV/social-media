<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\TableRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface INotificationRepository.
 */
interface INotificationRepository  {

    /**
     * Get the notification pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator;

    /**
     * Get the notification info.
     * @param Notification $notification
     * @return Notification
     */
    public function show(Notification $notification) :Notification;

    /**
    * Delete the notification.
    * @param UpdatePasswordRequest $request
    * @param Notification $notification
    * @return JsonResponse
    */
   public function destroy(Notification $notification) :JsonResponse;

}
