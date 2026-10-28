<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\TableRequest;
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
     * Get all of TicketSubject
     */
    public function index(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->index($request), Response::HTTP_OK);
    }
}
