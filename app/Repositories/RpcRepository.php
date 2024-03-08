<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Repositories\Contracts\IRpcRepository;
use App\Repositories\traits\GlobalFunc;
use Illuminate\Support\Facades\Auth;

class RpcRepository implements IRpcRepository {

    use GlobalFunc;

    /**
     * Get the necessary thing for user.
     * @return array
     */
    public function index() :array
    {
        $notifications = Notification::query()
            ->with('model')
            ->where('user_id', Auth::user()->id)
            ->where('status', 0)
            ->latest()
            ->limit(5)
            ->get();

        return [
            'notifications' => $notifications
        ];

    }
}
