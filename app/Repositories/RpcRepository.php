<?php

namespace App\Repositories;

use App\Http\Requests\TableRequest;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\IRpcRepository;
use App\Repositories\traits\GlobalFunc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class RpcRepository implements IRpcRepository {

    use GlobalFunc;

    /**
     * Get the necessary thing for user.
     * @return array
     */
    public function index() :array
    {
        $notifCount = Notification::query()
            ->where('user_id', Auth::user()->id)
            ->where('status', 0)
            ->count();

        return [
            'notification_count' => $notifCount
        ];

    }
}
