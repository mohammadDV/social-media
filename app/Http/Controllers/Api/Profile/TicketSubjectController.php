<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubjectRequest;
use App\Http\Requests\TableRequest;
use App\Models\TicketSubject;
use App\Repositories\Contracts\ITicketSubjectRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TicketSubjectController extends Controller
{
    /**
     * Constructor of TicketSubjectController.
     */
    public function __construct(protected ITicketSubjectRepository $repository)
    {
        //
    }

    /**
     * Get all of Subjects with pagination
     */
    public function indexPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get all of Subjects
     */
    public function index(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->index($request), Response::HTTP_OK);
    }

    /**
     * Get the subject.
     *
     * @param  TicketSubject  $subject
     */
    public function show(TicketSubject $ticketSubject): JsonResponse
    {
        return response()->json($this->repository->show($ticketSubject), Response::HTTP_OK);
    }

    /**
     * Store the subject.
     */
    public function store(SubjectRequest $request): JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Update the subject.
     */
    public function update(SubjectRequest $request, TicketSubject $ticketSubject): JsonResponse
    {
        return $this->repository->update($request, $ticketSubject);
    }

    /**
     * Delete the subject.
     *
     * @param  TicketSubject  $subject
     */
    public function destroy(TicketSubject $ticketSubject): JsonResponse
    {
        return $this->repository->destroy($ticketSubject);
    }
}
