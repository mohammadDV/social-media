<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\PageRequest;
use App\Http\Requests\PageUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface IPageRepository.
 */
interface IPageRepository  {

    /**
     * Get the page pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator;

    /**
     * Get the page info.
     * @param Page $page
     * @return Page
     */
    public function show(Page $page) :Page;

    /**
     * Store the Page.
     * @param PageRequest $request
     * @return JsonResponse
     */
    public function store(PageRequest $request) :JsonResponse;

    /**
     * Update the page.
     * @param PageUpdateRequest $request
     * @param Page $page
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(PageUpdateRequest $request, Page $page) :JsonResponse;

    /**
    * Delete the page.
    * @param UpdatePasswordRequest $request
    * @param Page $page
    * @return JsonResponse
    */
   public function destroy(Page $page) :JsonResponse;

}
