<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
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
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->repository->index(), Response::HTTP_OK);
    }
}
