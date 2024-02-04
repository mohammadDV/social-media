<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketRequest;
use App\Repositories\Contracts\ISportRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SportController extends Controller
{
    /**
     * Constructor of SportController.
     */
    public function __construct(protected ISportRepository $repository)
    {
        //
    }

    /**
     * Get all of ticket
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->repository->index(), Response::HTTP_OK);
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
}
