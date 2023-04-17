<?php


namespace App\Services;

use App\Models\Task;
use App\Models\Category;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class ReportService
{
    public const statuses = [
        Task::STATUS_OPEN,
        Task::STATUS_RESPONSE,
        Task::STATUS_IN_PROGRESS,
        Task::STATUS_COMPLETE,
        Task::STATUS_NOT_COMPLETED,
        Task::STATUS_CANCELLED
    ];

    /**
     *
     * Function  perf_ajax
     * Mazkur metod padcategoriyalar bo'yicha reportlar
     *
     */
    public function report(): JsonResponse
    {
        $query = Category::query()->where('parent_id', null);
        return Datatables::of($query)
            ->addColumn('sub_cat', function () {
                return '>';
            })
            ->addColumn('open_count', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("{$date}-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_OPEN)->get();
                return count($application);
            })
            ->addColumn('open_sum', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_OPEN)->pluck('budget')->toArray();
                return array_sum($application);
            })
            ->addColumn('response_count', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("{$date}-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_RESPONSE)->get();
                return count($application);
            })
            ->addColumn('response_sum', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_RESPONSE)->pluck('budget')->toArray();
                return array_sum($application);
            })
            ->addColumn('process_count', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_IN_PROGRESS)->get();
                return count($application);
            })
            ->addColumn('process_sum', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_IN_PROGRESS)->pluck('budget')
                    ->toArray();
                return array_sum($application);
            })
            ->addColumn('finished_count', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_COMPLETE)->get();
                return count($application);
            })
            ->addColumn('finished_sum', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_COMPLETE)->pluck('budget')->toArray();
                return array_sum($application);
            })
            ->addColumn('not_complete_count', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_NOT_COMPLETED)->get();
                return count($application);
            })
            ->addColumn('not_complete_sum', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_NOT_COMPLETED)->pluck('budget')->toArray();
                return array_sum($application);
            })
            ->addColumn('cancelled_count', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_CANCELLED)->get();
                return count($application);
            })
            ->addColumn('cancelled_sum', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("{$date}-31")->toDateTimeString();
                $end_date = Carbon::parse("{$date_1}-31")->toDateTimeString();
                $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_CANCELLED)->pluck('budget')->toArray();
                return array_sum($application);
            })
            ->addColumn('total_count', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("{$date}-31")->toDateTimeString();
                $end_date = Carbon::parse("{$date_1}-31")->toDateTimeString();
                $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->whereIn('status', self::statuses)
                    ->where('category_id', $cat)->get();
                return count($application);
            })
            ->addColumn('total_sum', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->whereIn('status', self::statuses)
                    ->where('category_id', $cat)->pluck('budget')->toArray();
                return array_sum($application);
            })->make();

    }

    /**
     *
     * Function  child_report
     * Mazkur metod childcategoriyalar bo'yicha reportlar
     * @param $id
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function child_report($id): JsonResponse
    {
        $query = Category::query()->where('parent_id', $id)->get();
        return Datatables::of($query)
            ->addColumn('open_count', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_OPEN)->get();
                return count($application);
            })
            ->addColumn('open_sum', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_OPEN)->pluck('budget')->toArray();
                return array_sum($application);
            })
            ->addColumn('response_count', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_RESPONSE)->get();
                return count($application);
            })
            ->addColumn('response_sum', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_RESPONSE)->pluck('budget')->toArray();
                return array_sum($application);
            })
            ->addColumn('process_count', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_IN_PROGRESS)->get();
                return count($application);
            })
            ->addColumn('process_sum', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_IN_PROGRESS)->pluck('budget')->toArray();
                return array_sum($application);
            })
            ->addColumn('finished_count', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_COMPLETE)->get();
                return count($application);
            })
            ->addColumn('finished_sum', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_COMPLETE)->pluck('budget')->toArray();
                return array_sum($application);
            })
            ->addColumn('not_complete_count', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_NOT_COMPLETED)->get();
                return count($application);
            })
            ->addColumn('not_complete_sum', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_NOT_COMPLETED)->pluck('budget')->toArray();
                return array_sum($application);
            })
            ->addColumn('cancelled_count', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_CANCELLED)->get();
                return count($application);
            })
            ->addColumn('cancelled_sum', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->where('category_id', $cat)->where('status', Task::STATUS_CANCELLED)->pluck('budget')->toArray();
                return array_sum($application);
            })
            ->addColumn('total_count', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->whereIn('status', self::statuses)
                    ->where('category_id', $cat)->get();
                return count($application);
            })
            ->addColumn('total_sum', function ($app) {
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("$date-31")->toDateTimeString();
                $end_date = Carbon::parse("$date_1-31")->toDateTimeString();
                $cat = Category::query()->where('id', $app->id)->pluck('id')->toarray();
                $application = Task::query()->whereBetween('created_at', [$start_date, $end_date])
                    ->whereIn('status', self::statuses)
                    ->where('category_id', $cat)->pluck('budget')->toArray();
                return array_sum($application);
            })
            ->make();
    }
}
