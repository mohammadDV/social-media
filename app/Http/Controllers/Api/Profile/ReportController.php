<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportCloseRequest;
use App\Http\Requests\ReportRequest;
use App\Http\Requests\TableRequest;
use App\Models\Report;
use App\Repositories\Contracts\IReportRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    /**
     * Constructor of ReportController.
     */
    public function __construct(protected  IReportRepository $repository)
    {
        //
    }

    /**
     * Get all of reports with pagination
     * @param TableRequest $request
     * @return JsonResponse
     */
    public function indexPaginate(TableRequest $request): JsonResponse
    {
        return response()->json($this->repository->indexPaginate($request), Response::HTTP_OK);
    }

    /**
     * Get all of reports
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->repository->index(), Response::HTTP_OK);
    }

    /**
     * Get the report.
     * @param
     * @return JsonResponse
     */
    public function show(Report $report) :JsonResponse
    {
        return response()->json($this->repository->show($report), Response::HTTP_OK);
    }

    /**
     * Store the report.
     * @param ReportRequest $request
     * @return JsonResponse
     */
    public function store(ReportRequest $request) :JsonResponse
    {
        return $this->repository->store($request);
    }

    /**
     * Update the report.
     * @param ReportCloseRequest $request
     * @param Report $report
     * @return JsonResponse
     */
    public function close(ReportCloseRequest $request, Report $report) :JsonResponse
    {
        return $this->repository->close($request, $report);
    }

    /**
     * Delete the report.
     * @param Report $report
     * @return JsonResponse
     */
    public function destroy(Report $report) :JsonResponse
    {
        return $this->repository->destroy($report);
    }
}
