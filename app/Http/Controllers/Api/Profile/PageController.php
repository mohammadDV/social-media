<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\PageRequest;
use App\Http\Requests\PageUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Models\Page;
use App\Repositories\Contracts\IPageRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PageController extends Controller
{
    /**
     * Constructor of PageController.
     */
    public function __construct(protected  IPageRepository $repository)
    {
        //
    }

    /**
     * Get all of page with pagination
     * @param TableRequest $request
     * @return JsonResponse
     */
    public function indexPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get the page.
     * @param Page $page
     * @return JsonResponse
     */
    public function show(Page $page) :JsonResponse
    {

        return response()->json($this->repository->show($page), Response::HTTP_OK);
    }

    /**
     * Store the page.
     * @param PageRequest $request
     * @return JsonResponse
     */
    public function store(PageRequest $request) :JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Update the page.
     * @param PageUpdateRequest $request
     * @param Page $page
     * @return JsonResponse
     */
    public function update(PageUpdateRequest $request, Page $page) :JsonResponse
    {
        return $this->repository->update($request, $page);
    }

    /**
     * Delete the page.
     * @param Page $page
     * @return JsonResponse
     */
    public function destroy(Page $page) :JsonResponse
    {
        return $this->repository->destroy($page);
    }
}
