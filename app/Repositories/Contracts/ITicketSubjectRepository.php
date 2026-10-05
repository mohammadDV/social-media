<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\SubjectRequest;
use App\Http\Requests\TableRequest;
use App\Models\TicketSubject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface ITicketSubjectRepository.
 */
interface ITicketSubjectRepository
{
    /**
     * Get the Subjects pagination.
     */
    public function indexPaginate(TableRequest $request): LengthAwarePaginator;

    /**
     * Get the Subjects.
     */
    public function index(): Collection;

    /**
     * Get the subject.
     */
    public function show(TicketSubject $subject): TicketSubject;

    /**
     * Store the subject.
     *
     * @throws \Exception
     */
    public function store(SubjectRequest $request): JsonResponse;

    /**
     * Update the subject.
     *
     * @throws \Exception
     */
    public function update(SubjectRequest $request, TicketSubject $subject): JsonResponse;

    /**
     * Delete the subject.
     */
    public function destroy(TicketSubject $subject): JsonResponse;
}
