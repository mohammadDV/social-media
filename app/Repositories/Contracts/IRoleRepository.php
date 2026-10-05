<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface IRoleRepository.
 */
interface IRoleRepository
{
    /**
     * Get the roles.
     *
     * @return Collection
     */
    public function roles();

    /**
     * Get the permissions.
     */
    public function permissions(): Collection;
}
