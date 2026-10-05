<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\TableRequest;
use App\Http\Requests\TicketRequest;
use App\Http\Requests\TicketStatusRequest;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface ITicketRepository.
 */
interface ITicketRepository
{
    /**
     * Get the tikets pagination.
     */
    public function indexPaginate(TableRequest $request): LengthAwarePaginator;

    /**
     * Store the ticket.
     *
     * @throws \Exception
     */
    public function store(TicketRequest $request): JsonResponse;

    /**
     * Change status of the ticket
     */
    public function changeStatus(TicketStatusRequest $request, Ticket $ticket): JsonResponse;

    /**
     * Get the sport.
     */
    public function show(Ticket $ticket): Ticket;
}
