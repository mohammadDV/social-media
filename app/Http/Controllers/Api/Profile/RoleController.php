<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IRoleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    /**
     * Constructor of RoleController.
     */
    public function __construct(protected  IRoleRepository $repository)
    {
        //
    }

    /**
     * Get all of roles
     * @return JsonResponse
     */
    public function roles(): JsonResponse
    {
        return response()->json($this->repository->roles(), Response::HTTP_OK);
    }

    /**
     * Get all of permissions
     * @return JsonResponse
     */
    public function permissions(): JsonResponse
    {
        return response()->json($this->repository->permissions(), Response::HTTP_OK);
    }
}
