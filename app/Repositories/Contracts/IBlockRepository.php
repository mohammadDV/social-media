<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\SearchRequest;
use App\Models\User;

/**
 * Interface IBlockRepository.
 */
interface IBlockRepository
{
    /**
     * Get the blocks users
     */
    public function index(SearchRequest $request);

    /**
     * Store the block
     */
    public function store(User $user): array;
}
