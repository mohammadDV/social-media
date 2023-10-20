<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
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
     * @return UserResource
     */
    public function show() :UserResource;

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

    /**
    * Update the password of user.
    * @param UpdatePasswordRequest $request
    * @param User $user
    * @return JsonResponse
    */
   public function updatePassword(UpdatePasswordRequest $request, User $user) :JsonResponse;

}
