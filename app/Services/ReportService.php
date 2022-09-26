<?php


namespace App\Services;

use App\Models\Task;
use App\Models\Category;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class ReportService
{
    /**
     *
     * Function  perf_ajax
     * Mazkur metod padcategoriyalar bo'yicha reportlar
     *
     */
    public function report()
    {
        $query = Category::query()->where('parent_id',null);
            return Datatables::of($query)

            ->addColumn('sub_cat', function($app){
                return '>';
            })
            ->addColumn('open_count', function($app){
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("{$date}-31")
                    ->toDateTimeString();

                $end_date = Carbon::parse("{$date_1}-31")
                    ->toDateTimeString();
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 1)->get();
                return count($application);
            })
            ->addColumn('open_sum', function($app){
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("{$date}-31")
                    ->toDateTimeString();

                $end_date = Carbon::parse("{$date_1}-31")
                    ->toDateTimeString();
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 1)->pluck('budget')->toArray();
                return array_sum($application);
            })

            ->addColumn('process_count', function($app){
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("{$date}-31")
                    ->toDateTimeString();

                $end_date = Carbon::parse("{$date_1}-31")
                    ->toDateTimeString();
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 3)->get();
                return count($application);
            })
            ->addColumn('process_sum', function($app){
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("{$date}-31")
                    ->toDateTimeString();

                $end_date = Carbon::parse("{$date_1}-31")
                    ->toDateTimeString();
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 3)->pluck('budget')->toArray();
                return array_sum($application);
            })

            ->addColumn('finished_count', function($app){
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("{$date}-31")
                    ->toDateTimeString();

                $end_date = Carbon::parse("{$date_1}-31")
                    ->toDateTimeString();
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 4)->get();
                return count($application);
            })
            ->addColumn('finished_sum', function($app){
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("{$date}-31")
                    ->toDateTimeString();

                $end_date = Carbon::parse("{$date_1}-31")
                    ->toDateTimeString();
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 4)->pluck('budget')->toArray();
                return array_sum($application);
            })

            ->addColumn('open_count', function($app){
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("{$date}-31")
                    ->toDateTimeString();

                $end_date = Carbon::parse("{$date_1}-31")
                    ->toDateTimeString();
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 1)->get();
                return count($application);
            })
            ->addColumn('open_sum', function($app){
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("{$date}-31")
                    ->toDateTimeString();

                $end_date = Carbon::parse("{$date_1}-31")
                    ->toDateTimeString();
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 1)->pluck('budget')->toArray();
                return array_sum($application);
            })

                ->addColumn('cencelled_count', function($app){
                    $date = Cache::get('date');
                    $date_1 = Cache::get('date_1');
                    $start_date = Carbon::parse("{$date}-31")
                        ->toDateTimeString();

                    $end_date = Carbon::parse("{$date_1}-31")
                        ->toDateTimeString();
                    $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                    $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 6)->get();
                    return count($application);
                })
                ->addColumn('cencelled_sum', function($app){
                    $date = Cache::get('date');
                    $date_1 = Cache::get('date_1');
                    $start_date = Carbon::parse("{$date}-31")
                        ->toDateTimeString();

                    $end_date = Carbon::parse("{$date_1}-31")
                        ->toDateTimeString();
                    $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                    $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 6)->pluck('budget')->toArray();
                    return array_sum($application);
                })

            ->addColumn('total_count', function($app){
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("{$date}-31")
                    ->toDateTimeString();

                $end_date = Carbon::parse("{$date_1}-31")
                    ->toDateTimeString();
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->get();
                return count($application);
            })
            ->addColumn('total_sum', function($app){
                $date = Cache::get('date');
                $date_1 = Cache::get('date_1');
                $start_date = Carbon::parse("{$date}-31")
                    ->toDateTimeString();

                $end_date = Carbon::parse("{$date_1}-31")
                    ->toDateTimeString();
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->pluck('budget')->toArray();
                return array_sum($application);
            })->make(true);

    }
        /**
         *
         * Function  child_report
         * Mazkur metod childcategoriyalar bo'yicha reportlar
         * @param $id  Object
         *
         */
            public function child_report($id) {
                $query = Category::where('parent_id',$id)->get();
                return Datatables::of($query)
                    ->addColumn('open_count', function($app){
                        $date = Cache::get('date');
                        $date_1 = Cache::get('date_1');
                        $start_date = Carbon::parse("{$date}-31")
                            ->toDateTimeString();

                        $end_date = Carbon::parse("{$date_1}-31")
                            ->toDateTimeString();
                        $cat = Category::where('id', $app->id)->pluck('id')->toarray();
                        $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 1)->get();
                        return count($application);
                    })
                    ->addColumn('open_sum', function($app){
                        $date = Cache::get('date');
                        $date_1 = Cache::get('date_1');
                        $start_date = Carbon::parse("{$date}-31")
                            ->toDateTimeString();

                        $end_date = Carbon::parse("{$date_1}-31")
                            ->toDateTimeString();
                        $cat = Category::where('id', $app->id)->pluck('id')->toarray();
                        $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 1)->pluck('budget')->toArray();
                        return array_sum($application);
                    })

                    ->addColumn('process_count', function($app){
                        $date = Cache::get('date');
                        $date_1 = Cache::get('date_1');
                        $start_date = Carbon::parse("{$date}-31")
                            ->toDateTimeString();

                        $end_date = Carbon::parse("{$date_1}-31")
                            ->toDateTimeString();
                        $cat = Category::where('id', $app->id)->pluck('id')->toarray();
                        $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 3)->get();
                        return count($application);
                    })
                    ->addColumn('process_sum', function($app){
                        $date = Cache::get('date');
                        $date_1 = Cache::get('date_1');
                        $start_date = Carbon::parse("{$date}-31")
                            ->toDateTimeString();

                        $end_date = Carbon::parse("{$date_1}-31")
                            ->toDateTimeString();
                        $cat = Category::where('id', $app->id)->pluck('id')->toarray();
                        $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 3)->pluck('budget')->toArray();
                        return array_sum($application);
                    })

                    ->addColumn('finished_count', function($app){
                        $date = Cache::get('date');
                        $date_1 = Cache::get('date_1');
                        $start_date = Carbon::parse("{$date}-31")
                            ->toDateTimeString();

                        $end_date = Carbon::parse("{$date_1}-31")
                            ->toDateTimeString();
                        $cat = Category::where('id', $app->id)->pluck('id')->toarray();
                        $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 4)->get();
                        return count($application);
                    })
                    ->addColumn('finished_sum', function($app){
                        $date = Cache::get('date');
                        $date_1 = Cache::get('date_1');
                        $start_date = Carbon::parse("{$date}-31")
                            ->toDateTimeString();

                        $end_date = Carbon::parse("{$date_1}-31")
                            ->toDateTimeString();
                        $cat = Category::where('id', $app->id)->pluck('id')->toarray();
                        $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 4)->pluck('budget')->toArray();
                        return array_sum($application);
                    })

                    ->addColumn('open_count', function($app){
                        $date = Cache::get('date');
                        $date_1 = Cache::get('date_1');
                        $start_date = Carbon::parse("{$date}-31")
                            ->toDateTimeString();

                        $end_date = Carbon::parse("{$date_1}-31")
                            ->toDateTimeString();
                        $cat = Category::where('id', $app->id)->pluck('id')->toarray();
                        $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 1)->get();
                        return count($application);
                    })
                    ->addColumn('open_sum', function($app){
                        $date = Cache::get('date');
                        $date_1 = Cache::get('date_1');
                        $start_date = Carbon::parse("{$date}-31")
                            ->toDateTimeString();

                        $end_date = Carbon::parse("{$date_1}-31")
                            ->toDateTimeString();
                        $cat = Category::where('id', $app->id)->pluck('id')->toarray();
                        $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 1)->pluck('budget')->toArray();
                        return array_sum($application);
                    })

                    ->addColumn('cencelled_count', function($app){
                        $date = Cache::get('date');
                        $date_1 = Cache::get('date_1');
                        $start_date = Carbon::parse("{$date}-31")
                            ->toDateTimeString();

                        $end_date = Carbon::parse("{$date_1}-31")
                            ->toDateTimeString();
                        $cat = Category::where('id', $app->id)->pluck('id')->toarray();
                        $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 6)->get();
                        return count($application);
                    })
                    ->addColumn('cencelled_sum', function($app){
                        $date = Cache::get('date');
                        $date_1 = Cache::get('date_1');
                        $start_date = Carbon::parse("{$date}-31")
                            ->toDateTimeString();

                        $end_date = Carbon::parse("{$date_1}-31")
                            ->toDateTimeString();
                        $cat = Category::where('id', $app->id)->pluck('id')->toarray();
                        $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->where('status', 6)->pluck('budget')->toArray();
                        return array_sum($application);
                    })

                    ->addColumn('total_count', function($app){
                        $date = Cache::get('date');
                        $date_1 = Cache::get('date_1');
                        $start_date = Carbon::parse("{$date}-31")
                            ->toDateTimeString();

                        $end_date = Carbon::parse("{$date_1}-31")
                            ->toDateTimeString();
                        $cat = Category::where('id', $app->id)->pluck('id')->toarray();
                        $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->get();
                        return count($application);
                    })
                    ->addColumn('total_sum', function($app){
                        $date = Cache::get('date');
                        $date_1 = Cache::get('date_1');
                        $start_date = Carbon::parse("{$date}-31")
                            ->toDateTimeString();

                        $end_date = Carbon::parse("{$date_1}-31")
                            ->toDateTimeString();
                        $cat = Category::where('id', $app->id)->pluck('id')->toarray();
                        $application = Task::whereBetween('created_at', [$start_date, $end_date])->where('category_id', $cat)->pluck('budget')->toArray();
                        return array_sum($application);
                    })
                    ->make(true);
                }

}
