<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\SearchRequest;
use App\Repositories\Contracts\IUserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
     * Handle an incoming authentication request.
     */
    public function profile(): JsonResponse
    {
        return response()->json(Auth::user());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }

    /**
     * Search users.
     * @param SearchRequest $request
     */
    public function search(SearchRequest $request): JsonResponse
    {
        return response()->json($this->repository->search($request));
    }
}
