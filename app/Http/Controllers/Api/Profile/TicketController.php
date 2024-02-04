<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\SportRequest;
use App\Http\Requests\SportUpdateRequest;
use App\Http\Requests\TableRequest;
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
     * Get the sport.
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
     * Update the sport.
     * @param SportUpdateRequest $request
     * @param Sport $sport
     * @return JsonResponse
     */
    public function update(SportUpdateRequest $request, Sport $sport) :JsonResponse
    {
        return $this->repository->update($request, $sport);
    }

    /**
     * Delete the sport.
     * @param Sport $sport
     * @return JsonResponse
     */
    public function destroy(Sport $sport) :JsonResponse
    {
        return $this->repository->destroy($sport);
    }
}
