<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\ReportCloseRequest;
use App\Http\Requests\ReportRequest;
use App\Http\Requests\TableRequest;
use App\Models\Report;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Interface IReportRepository.
 */
interface IReportRepository
{
    /**
     * Get the reports.
     */
    public function index(): array;

    /**
     * Get the reports pagination.
     */
    public function indexPaginate(TableRequest $request): LengthAwarePaginator;

    /**
     * Get the report.
     */
    public function show(Report $report): Report;

    /**
     * Store the report.
     *
     * @throws \Exception
     */
    public function store(ReportRequest $request): JsonResponse;

    /**
     * Update the report.
     *
     * @throws \Exception
     */
    public function close(ReportCloseRequest $request, Report $report): JsonResponse;

    /**
     * Delete the report.
     *
     * @param  UpdatePasswordRequest  $request
     */
    public function destroy(Report $report): JsonResponse;
}
