<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ICategoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    /**
     * Constructor of CategoryController.
     */
    public function __construct(protected ICategoryRepository $repository)
    {
        //
    }

    /**
     * Get the active categories.
     */
    public function getActives(): JsonResponse
    {
        return response()->json($this->repository->getActives(), Response::HTTP_OK);
    }

    /**
     * Get the team categories.
     */
    public function getTeamCategories(): JsonResponse
    {
        return response()->json($this->repository->getTeamCategories(), Response::HTTP_OK);
    }

    /**
     * Get all.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->repository->index(), Response::HTTP_OK);
    }
}