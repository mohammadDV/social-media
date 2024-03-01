<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Page;
use App\Repositories\Contracts\IPageRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PageController extends Controller
{
    /**
     * Constructor of PageController.
     */
    public function __construct(protected IPageRepository $repository)
    {
        //
    }

    /**
     * Get all of active pages
     */
    public function getActivePages(): JsonResponse
    {
        return response()->json($this->repository->getActivePages(), Response::HTTP_OK);
    }

    /**
     * Get the active page.
     * @param string $slug
     * @return Page
     */
    public function getActivePage(string $slug) : JsonResponse
    {
        return response()->json($this->repository->getActivePage($slug), Response::HTTP_OK);
    }

}
