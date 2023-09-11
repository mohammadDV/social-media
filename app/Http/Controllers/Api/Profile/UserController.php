<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\Contracts\IUserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Constructor of UserController.
     */
    public function __construct(protected  IUserRepository $repository)
    {
        //
    }

    /**
     * Get all of users with pagination
     */
    public function indexPaginate(): JsonResponse
    {
        return response()->json($this->repository->indexPaginate(), Response::HTTP_OK);
    }

    /**
     * Get the user.
     * @return JsonResponse
     */
    public function show() :JsonResponse
    {
        return response()->json($this->repository->show(), Response::HTTP_OK);
    }

    /**
     * Store the user.
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request) :JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Update the user.
     * @param UpdateUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user) :JsonResponse
    {
        return $this->repository->update($request, $user);
    }

    /**
    * Update the password of user.
    * @param UpdatePasswordRequest $request
    * @param User $user
    * @return JsonResponse
    */
   public function updatePassword(UpdatePasswordRequest $request, User $user) :JsonResponse
   {
        return $this->repository->updatePassword($request, $user);
   }

}
