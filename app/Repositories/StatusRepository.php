<?php

namespace App\Repositories;

use App\Models\Status;
use App\Models\User;
use App\Repositories\Contracts\IStatusRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class StatusRepository implements IStatusRepository {

    /**
     * Get the status.
     * @param ?User $user
     * @return LengthAwarePaginator
     */
    public function index(?User $user) :LengthAwarePaginator
    {
        // ->addMinutes('1'),
        return cache()->remember("status.all" . !empty($user) ? $user?->id : '', now(),
            function () use($user){
            return Status::query()
                ->when(!empty($user->id), function ($query) use($user) {
                    return $query->where('user_id', $user->id);
                })
                ->with(['comments','likes','user'])
                ->where('status',1)
                ->orderBy('id', 'DESC')->paginate(100);
        });
    }
}
