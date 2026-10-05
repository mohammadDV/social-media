<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface IUserRepository.
 */
interface IUserRepository
{
    /**
     * Get the users
     */
    public function indexPaginate(Request $request): LengthAwarePaginator;

    /**
     * Get the user.
     */
    public function show(?User $user): UserResource;

    /**
     * Store the user.
     */
    public function store(UserRequest $request): JsonResponse;

    /**
     * Update the user.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse;

    /**
     * Update the password of user.
     */
    public function updatePassword(UpdatePasswordRequest $request, User $user): JsonResponse;

    /**
     * Search users.
     *
     * @param  LengthAwarePaginator  $request
     */
    public function search(SearchRequest $request): LengthAwarePaginator|array;

    /**
     * Report the user.
     */
    public function destroy(User $user): JsonResponse;
}
