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
    public function __construct(protected IUserRepository $repository)
    {
        //
    }

    /**
     * Get all of users with pagination
     */
    public function indexPaginate(Request $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get the user.
     */
    public function show(?User $user): JsonResponse
    {
        return response()->json($this->repository->show($user), Response::HTTP_OK);
    }

    /**
     * Store the user.
     */
    public function store(UserRequest $request): JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Update the user.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        return $this->repository->update($request, $user);
    }

    /**
     * Update the password of user.
     */
    public function updatePassword(UpdatePasswordRequest $request, User $user): JsonResponse
    {
        return $this->repository->updatePassword($request, $user);
    }

    /**
     * Report the user.
     */
    public function destroy(User $user): JsonResponse
    {
        return $this->repository->destroy($user);
    }
}
