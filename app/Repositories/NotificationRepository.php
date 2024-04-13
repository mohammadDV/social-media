<?php

namespace App\Repositories;

use App\Http\Requests\SendNotificationRequest;
use App\Http\Requests\TableRequest;
use App\Models\Notification;
use App\Models\NotificationSend;
use App\Models\User;
use App\Repositories\Contracts\INotificationRepository;
use App\Repositories\traits\GlobalFunc;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationRepository implements INotificationRepository {

    use GlobalFunc;

    /**
     * Get the notification pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator
    {

        Notification::query()
            ->where('user_id', Auth::user()->id)
            ->where('status', 0)
            ->where('type', Notification::STATUS_SIMPLE)
            ->update([
                'status' => 1
            ]);


        return Notification::query()
            ->with('model')
            ->where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->paginate($request->get('rowsPerNotification', 25));
    }

    /**
     * Get all notification sends.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function sendListPaginate(TableRequest $request) :LengthAwarePaginator
    {

        $search = $request->get('query');
        return NotificationSend::query()
            ->when(Auth::user()->level != 3, function ($query) {
                return $query->where('user_id', Auth::user()->id);
            })
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('message', 'like', '%' . $search . '%');
            })
            ->orderBy($request->get('sortBy', 'id'), $request->get('sortType', 'desc'))
            ->paginate($request->get('rowsPerPage', 25));
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
     * Check the notification users count
     * @param SendNotificationRequest $request
     * @return JsonResponse
     */
    public function checkNotificationCount(SendNotificationRequest $request) :array
    {

        $roleIds = $request->roles;
        $userIds = $request->users;

        return ['count' => $this->getUsersForNotification($roleIds, $userIds)->count()];
    }

    /**
     * Send a notification.
     * @param SendNotificationRequest $request
     * @return JsonResponse
     */
    public function sendAsAdmin(SendNotificationRequest $request) :JsonResponse
    {

        $roleIds = $request->roles;
        $userIds = $request->users;

        $users = $this->getUsersForNotification($roleIds, $userIds);

        $data = [
            'users' => $userIds ?? '',
            'roles' => $roleIds ?? '',
        ];

        $notificationSend = new NotificationSend();
        $notificationSend->user_id = Auth::user()->id;
        $notificationSend->conditions = $data;
        $notificationSend->users_count = $users->count();
        $notificationSend->save();

        $this->sendNotification($notificationSend, $users, $request);

        if ($notificationSend) {

            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }
    }

    /**
    * Get users for notification
    * @param array|null $roles
    * @param array|null $users
    * @return Builder
    */
   private function getUsersForNotification(array|null $roleIds, array|null $userIds) :Builder
   {
        return User::query()
                ->where('role_id', '!=', 1)
                ->when(!empty($roleIds), function ($query) use($roleIds) {
                    $query->whereIn('role_id', $roleIds);
                })
                ->when(!empty($userIds), function ($query) use($userIds) {
                    $query->whereIn('id', $userIds);
                });
   }

    /**
    * Send notifications.
    * @param NotificationSend $notificationSend
    * @param Builder $users
    * @param SendNotificationRequest $request
    * @return JsonResponse
    */
   private function sendNotification(NotificationSend $notificationSend, Builder $users, SendNotificationRequest $request)
   {

    DB::beginTransaction();

        try {
            $sendCount = 0;

            // Chunk the users and process them
            $users->chunk(200, function ($rows) use($request, &$sendCount) {
                $dataToInsert = [];

                // Prepare data for bulk insertion
                foreach ($rows as $user) {
                    $dataToInsert[] = [
                        'link' => $request->link,
                        'message' => $request->message,
                        'is_admin' => true,
                        'has_modal' => $request->has_modal,
                        'has_email' => $request->has_email,
                        'user_id' => $user->id,
                        'model_id' => Auth::user()->id,
                        'model_type' => User::class,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $sendCount++;
                }

                // Insert data in bulk
                Notification::insert($dataToInsert);

            });

            if ($sendCount) {
                $notificationSend->send_count = $sendCount;
                $notificationSend->status = 1;
                $notificationSend->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            DB::rollBack();
        }
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
