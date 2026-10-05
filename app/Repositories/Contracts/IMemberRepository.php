<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Interface IMemberRepository.
 */
interface IMemberRepository
{
    /**
     * Get the new members
     */
    public function getNewMembers(): AnonymousResourceCollection;

    /**
     * Get the congenial members
     */
    public function getCongenialMembers(): AnonymousResourceCollection;

    /**
     * Get the member info
     */
    public function getMemberInfo(User $user): array;
}
