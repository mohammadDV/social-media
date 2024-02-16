<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\TableRequest;
use App\Http\Requests\TicketMessageRequest;
use App\Http\Requests\TicketRequest;
use App\Http\Requests\TicketStatusRequest;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

 /**
 * Interface ITicketRepository.
 */
interface ITicketRepository  {

    /**
     * Get the tikets pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator;

    /**
     * Store the ticket.
     * @param TicketRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(TicketRequest $request) :JsonResponse;

    /**
     * Change status of the ticket
     * @param TicketStatusRequest $request
     * @param Ticket $ticket
     * @return JsonResponse
     */
    public function changeStatus(TicketStatusRequest $request, Ticket $ticket) :JsonResponse;

    /**
     * Store the message of ticket.
     * @param TicketMessageRequest $request
     * @param Ticket $ticket
     * @return JsonResponse
     * @throws \Exception
     */
    public function storeMessage(TicketMessageRequest $request, Ticket $ticket) :JsonResponse;

    /**
     * Get the sport.
     * @param Ticket $ticket
     * @return Ticket
     */
    public function show(Ticket $ticket) :Ticket;

}
