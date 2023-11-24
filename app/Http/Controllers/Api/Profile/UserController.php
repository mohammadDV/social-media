<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\Contracts\IUserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * @param Request $request
     */
    public function indexPaginate(Request $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get the user.
     * @param int $id
     * @return JsonResponse
     */
    public function show($id = 0) :JsonResponse
    {
        return response()->json($this->repository->show($id), Response::HTTP_OK);
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
