<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\TableRequest;
use App\Http\Requests\TicketMessageRequest;
use App\Http\Requests\TicketRequest;
use App\Http\Requests\TicketStatusRequest;
use App\Models\Ticket;
use App\Repositories\Contracts\ITicketRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TicketController extends Controller
{
    /**
     * Constructor of SportController.
     */
    public function __construct(protected ITicketRepository $repository)
    {
        //
    }

    /**
     * Get all of sports with pagination
     */
    public function indexPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get the ticket.
     */
    public function show(Ticket $ticket): JsonResponse
    {
        return response()->json($this->repository->show($ticket), Response::HTTP_OK);
    }

    /**
     * Store the ticket.
     */
    public function store(TicketRequest $request): JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Change status of the ticket
     */
    public function changeStatus(TicketStatusRequest $request, Ticket $ticket): JsonResponse
    {
        return $this->repository->changeStatus($request, $ticket);
    }

    /**
     * Store the message of ticket.
     *
     * @throws \Exception
     */
    public function storeMessage(TicketMessageRequest $request, Ticket $ticket): JsonResponse
    {
        return $this->repository->storeMessage($request, $ticket);
    }

    /**
     * Delete the ticket.
     *
     * @return JsonResponse
     */
    public function destroy(Ticket $ticket)
    {
        // return $this->repository->destroy($ticket);
    }
}
