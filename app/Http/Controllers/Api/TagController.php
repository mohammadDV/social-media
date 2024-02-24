<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Repositories\Contracts\ITagRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TagController extends Controller
{
    /**
     * Constructor of TagController.
     */
    public function __construct(protected ITagRepository $repository)
    {
        //
    }

    /**
     * Get the random tags
     */
    public function getRandom(): JsonResponse
    {
        return response()->json($this->repository->getRandom(), Response::HTTP_OK);
    }

    /**
     * Get all contents that has this tag
     * @param Tag $tag
     */
    public function index(Tag $tag): JsonResponse
    {
        return response()->json($this->repository->index($tag), Response::HTTP_OK);
    }
}
