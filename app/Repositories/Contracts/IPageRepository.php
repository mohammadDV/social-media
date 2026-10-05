<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\PageRequest;
use App\Http\Requests\PageUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface IPageRepository.
 */
interface IPageRepository
{
    /**
     * Get the page pagination.
     */
    public function indexPaginate(TableRequest $request): LengthAwarePaginator;

    /**
     * Get active pages.
     */
    public function getActivePages(): Collection;

    /**
     * Get the active page.
     */
    public function getActivePage(string $slug): ?Page;

    /**
     * Get the page info.
     */
    public function show(Page $page): Page;

    /**
     * Store the Page.
     */
    public function store(PageRequest $request): JsonResponse;

    /**
     * Update the page.
     *
     * @throws \Exception
     */
    public function update(PageUpdateRequest $request, Page $page): JsonResponse;

    /**
     * Delete the page.
     *
     * @param  UpdatePasswordRequest  $request
     */
    public function destroy(Page $page): JsonResponse;
}
