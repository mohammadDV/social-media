<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\SportRequest;
use App\Http\Requests\SportUpdateRequest;
use App\Http\Requests\TableRequest;
use App\Http\Requests\TicketMessageRequest;
use App\Http\Requests\TicketRequest;
use App\Models\Ticket;
use App\Repositories\Contracts\ITicketRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TicketController extends Controller
{
    /**
     * Constructor of SportController.
     */
    public function __construct(protected  ITicketRepository $repository)
    {
        //
    }

    /**
     * Get all of sports with pagination
     * @param TableRequest $request
     * @return JsonResponse
     */
    public function indexPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get the ticket.
     * @param
     * @return JsonResponse
     */
    public function show(Ticket $ticket) :JsonResponse
    {
        return response()->json($this->repository->show($ticket), Response::HTTP_OK);
    }

    /**
     * Store the ticket.
     * @param TicketRequest $request
     * @return JsonResponse
     */
    public function store(TicketRequest $request) :JsonResponse
    {
        return $this->repository->store($request);
    }

     /**
     * Store the message of ticket.
     * @param TicketMessageRequest $request
     * @param Ticket $ticket
     * @return JsonResponse
     * @throws \Exception
     */
    public function storeMessage(TicketMessageRequest $request, Ticket $ticket) :JsonResponse
    {
        return $this->repository->storeMessage($request, $ticket);
    }

    /**
     * Delete the ticket.
     * @param Ticket $ticket
     * @return JsonResponse
     */
    public function destroy(Ticket $ticket)
    {
        // return $this->repository->destroy($ticket);
    }
}
