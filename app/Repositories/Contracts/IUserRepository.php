<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IUserRepository.
 */
interface IUserRepository  {

    /**
     * Get the users
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(Request $request) :LengthAwarePaginator;

    /**
     * Get the user.
     * @param int $id
     * @return UserResource
     */
    public function show(int $id = 0) :UserResource;

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

    /**
    * Search users.
    * @param LengthAwarePaginator $request
    * @return LengthAwarePaginator|array
    */
   public function search(SearchRequest $request) :LengthAwarePaginator|array;

}
