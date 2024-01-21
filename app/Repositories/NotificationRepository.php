<?php

namespace App\Repositories;

use App\Http\Requests\TableRequest;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\INotificationRepository;
use App\Repositories\traits\GlobalFunc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class NotificationRepository implements INotificationRepository {

    use GlobalFunc;

    /**
     * Get the notification pagination.
     * @param TableRequest $request
     * @param ?User $user
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request, ?User $user) :LengthAwarePaginator
    {
        if (empty($user->id)) {
            $user = Auth::user();
        }

        Notification::query()
            ->where('user_id', $user->id)
            ->where('status', 0)
            ->where('type', Notification::STATUS_SIMPLE)
            ->update([
                'status' => 1
            ]);


        return Notification::query()
            ->with('model')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate($request->get('rowsPerNotification', 2));
    }

    /**
     * Get the notification info.
     * @param Notification $notification
     * @return Matches
     */
    public function show(Notification $notification) :Notification
    {
        return $notification;
    }

    /**
    * Delete the notification.
    * @param Notification $notification
    * @return JsonResponse
    */
   public function destroy(Notification $notification) :JsonResponse
   {
        $this->checkLevelAccess(Auth::user()->id == $notification->user_id);

        $notification->delete();

        if ($notification) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
   }
}
