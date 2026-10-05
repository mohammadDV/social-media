<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Interface ICategoryRepository.
 */
interface ICategoryRepository
{
    /**
     * Get the active categories.
     */
    public function getActives(): AnonymousResourceCollection;

    /**
     * Get the poular categories.
     */
    public function popularCategories(): AnonymousResourceCollection;

    /**
     * Get the team categories.
     */
    public function getTeamCategories(): AnonymousResourceCollection;

    /**
     * Get all.
     */
    public function index(): AnonymousResourceCollection;
}
