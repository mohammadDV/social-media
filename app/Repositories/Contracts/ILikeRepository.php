<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

/**
 * Interface ILikeRepository.
 */
interface ILikeRepository
{
    /**
     * Get the all likes
     */
    public function getLikes(int $id, string $type): Collection;

    /**
     * Get the like
     */
    public function store(int $id, string $type): JsonResponse;

    /**
     * Get the count of likes
     */
    public function getCount(int $id, string $type): int;
}
