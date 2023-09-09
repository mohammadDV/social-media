<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IUserRepository.
 */
interface IUserRepository  {

    /**
     * Get the users pagination.
     * @return LengthAwarePaginator
     */
    public function indexPaginate() :LengthAwarePaginator;

    /**
     * Get the user.
     * @return User
     */
    public function show() :User;

    /**
     * Store the user.
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request) :JsonResponse;

    /**
     * Update the user.
     * @param UpdateUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user) :JsonResponse;

}
