<?php

namespace App\Repositories;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\IRoleRepository;
use App\Repositories\traits\GlobalFunc;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class RoleRepository implements IRoleRepository {

    use GlobalFunc;

    /**
     * Get the roles.
     * @return Collection
     */
    public function roles()
    {
        return Role::query()
            ->when(Auth::user()->role_id != 1, function ($query) {
                $query->where('name', '!=', 'admin');
            })
            ->get();
    }

    /**
     * Get the permissions.
     * @return Collection
     */
    public function permissions() :Collection
    {
        return Permission::all();
    }
}
