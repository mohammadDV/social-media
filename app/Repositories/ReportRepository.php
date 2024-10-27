<?php

namespace App\Repositories;

use App\Http\Requests\ReportCloseRequest;
use App\Http\Requests\ReportRequest;
use App\Http\Requests\TableRequest;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Report;
use App\Models\Status;
use App\Repositories\Contracts\IReportRepository;
use App\Repositories\traits\GlobalFunc;
use App\Services\TelegramNotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ReportRepository implements IReportRepository {


    use GlobalFunc;

    /**
     * @param TelegramNotificationService $service
     */
    public function __construct(protected TelegramNotificationService $service)
    {

    }

    /**
     * Get the report.
     * @return array
     */
    public function index() :array
    {
        return [];

    }

    /**
     * Get the reports pagination.
     * @param TableRequest $request
     * @return LengthAwarePaginator
     */
    public function indexPaginate(TableRequest $request) :LengthAwarePaginator
    {
        $search = $request->get('query');
        return Report::query()
            ->with('model')
            ->when(Auth::user()->level != 3, function ($query) {
                return $query->where('user_id', Auth::user()->id);
            })
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('message', 'like', '%' . $search . '%')
                    ->orWhere('id', $search);
            })
            ->orderBy($request->get('sortBy', 'status'), $request->get('sortType', 'desc'))
            ->paginate($request->get('rowsPerPage', 25));
    }

    /**
     * Get the report.
     * @param Report $report
     * @return Report
     */
    public function show(Report $report) :Report
    {
        return Report::query()
                ->with('model')
                ->where('id', $report->id)
                ->first();
    }

    /**
     * Store the report.
     * @param ReportRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(ReportRequest $request) :JsonResponse
    {

        $report = Report::query()
            ->where('user_id', Auth::user()->id)
            ->where('status', Report::STATUS_PENDING)
            ->orderBy('id', 'desc')
            ->first();

        if ($report) {
            $createdAt = Carbon::parse($report->updated_at);

            // Check if created_at is more than 2 minutes ago
            if ($createdAt->diffInMinutes(Carbon::now()) < config('times.report_time_min')) {
                return response()->json([
                    'status' => 0,
                    'message' => __('site.You are not allowed to resend messages. Please try again in 2 minutes.', ['number' => config('times.report_time_min')])
                ]);
            }
        }


        $type = ucfirst($request->input('type'));

        $report = Report::updateOrCreate([
            'model_id'   => $request->input('id'),
            'model_type' => "App\\Models\\" . $type,
            'status'     => Report::STATUS_PENDING
        ], [
            'message'    => $request->input('message'),
            'user_id'    => Auth::user()->id,
        ]);

        $report->increment('count');

        $this->service->sendNotification(
            config('telegram.chat_id'),
            sprintf('ارسال یک گزارش از %s با نام کاربری %s', Auth::user()->id, Auth::user()->nickname) . PHP_EOL .
            $request->input('message')
        );

        if ($report) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();
    }

    /**
     * Update the report.
     * @param ReportCloseRequest $request
     * @param Report $report
     * @return JsonResponse
     * @throws \Exception
     */
    public function close(ReportCloseRequest $request, Report $report) :JsonResponse
    {
        $this->checkLevelAccess();

        if ($report->status != Report::STATUS_PENDING) {
            throw new \Exception();
        }

        if (!empty($request->is_delete)) {
            $report->model->update([
                'is_report' => $request->is_delete
            ]);

            $this->sendNotification($report->model);
        }

        $update = $report->update([
            'status' => Report::STATUS_CLOSED,
            'is_delete' => $request->is_delete,
            'operator_id' => Auth::user()->id,
        ]);

        if ($update) {

            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully'),
                'data' => $report
            ], Response::HTTP_OK);
        }

        throw new \Exception();
    }

    /**
    * Delete the report.
    * @return JsonResponse
    */
   public function sendNotification($model) :void
   {
        switch ($model::class) {
            case Comment::class;
                $status = true;
                $message = __('site.Dear user. According to user reports, your media has been removed from public view due to violation of application rules. Please try to publish the content within the framework of the defined rules. Thank you for your cooperation.',
                    ['media' => __('site.Comment')]);
            break;
            case Status::class;
                $status = true;
                $message = __('site.Dear user. According to user reports, your media has been removed from public view due to violation of application rules. Please try to publish the content within the framework of the defined rules. Thank you for your cooperation.',
                    ['media' => __('site.Status post')]);
            break;
            default;
                $status = false;
        }

        if ($status) {
            // Add notification
            Notification::create([
                'message' => $message,
                'link' => '',
                'user_id' => $model->user_id,
                'model_id' => $model->id,
                'model_type' => $model::class,
            ]);
        }
   }

    /**
    * Delete the report.
    * @param UpdatePasswordRequest $request
    * @param Report $report
    * @return JsonResponse
    */
   public function destroy(Report $report) :JsonResponse
   {
        $this->checkLevelAccess(Auth::user()->id == $report->user_id);

        $report->delete();

        if ($report) {
            return response()->json([
                'status' => 1,
                'message' => __('site.The operation has been successfully')
            ], Response::HTTP_OK);
        }

        throw new \Exception();
   }
}
