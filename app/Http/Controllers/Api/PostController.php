<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IPostRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Log in the user.
     */
    public function __construct(protected IPostRepository $repository)
    {
        //
    }

    /**
     * Get all of post except newspaper.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->repository->index(range(1,7), 100), Response::HTTP_OK);
    }
}
