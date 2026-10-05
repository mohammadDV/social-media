<?php

namespace App\Repositories\Contracts;

/**
 * Interface IRpcRepository.
 */
interface IRpcRepository
{
    /**
     * Get the necessary thing for user.
     */
    public function index(): array;
}
