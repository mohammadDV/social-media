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
interface IReportRepository  {

    /**
     * Get the reports.
     * @return array
     */
    public function index() :array;

    /**
     * Get the reports pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator;

    /**
     * Get the report.
     * @param Report $report
     * @return Report
     */
    public function show(Report $report) :Report;

    /**
     * Store the report.
     * @param ReportRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(ReportRequest $request) :JsonResponse;

    /**
     * Update the report.
     * @param ReportCloseRequest $request
     * @param Report $report
     * @return JsonResponse
     * @throws \Exception
     */
    public function close(ReportCloseRequest $request, Report $report) :JsonResponse;

    /**
    * Delete the report.
    * @param UpdatePasswordRequest $request
    * @param Report $report
    * @return JsonResponse
    */
   public function destroy(Report $report) :JsonResponse;

}
