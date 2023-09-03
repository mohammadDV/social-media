<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IMemberRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MemberController extends Controller
{
    /**
     * Constructor of StatusController.
     */
    public function __construct(protected IMemberRepository $repository)
    {
        //
    }

    /**
     * Get the new members.
     */
    public function getNewMembers(): JsonResponse
    {
        return response()->json($this->repository->getNewMembers(), Response::HTTP_OK);
    }

    /**
     * Get the congenial members.
     */
    public function getCongenialMembers(): JsonResponse
    {
        return response()->json($this->repository->getCongenialMembers(), Response::HTTP_OK);
    }
}
