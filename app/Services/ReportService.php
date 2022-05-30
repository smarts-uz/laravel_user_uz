<?php


namespace App\Services;

use App\Models\Task;
use App\Models\Report;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Item\ReportItem;
use Yajra\DataTables\DataTables;

class ReportService
{
    public function report()
    {
        $query = Category::query()->where('parent_id',null);
            return Datatables::of($query)

            ->addColumn('sub_cat', function($app){
                return '>';
            })
            ->addColumn('open_count', function($app){
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::where('category_id', $cat)->where('status', 1)->get();
                return count($application);
            })
            ->addColumn('open_sum', function($app){
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::where('category_id', $cat)->where('status', 1)->pluck('budget')->toArray();
                return array_sum($application);
            })

            ->addColumn('process_count', function($app){
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::where('category_id', $cat)->where('status', 3)->get();
                return count($application);
            })
            ->addColumn('process_sum', function($app){
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::where('category_id', $cat)->where('status', 3)->pluck('budget')->toArray();
                return array_sum($application);
            })

            ->addColumn('finished_count', function($app){
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::where('category_id', $cat)->where('status', 4)->get();
                return count($application);
            })
            ->addColumn('finished_sum', function($app){
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::where('category_id', $cat)->where('status', 4)->pluck('budget')->toArray();
                return array_sum($application);
            })

            ->addColumn('open_count', function($app){
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::where('category_id', $cat)->where('status', 1)->get();
                return count($application);
            })
            ->addColumn('open_sum', function($app){
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::where('category_id', $cat)->where('status', 1)->pluck('budget')->toArray();
                return array_sum($application);
            })

            ->addColumn('total_count', function($app){
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::where('category_id', $cat)->get();
                return count($application);
            })
            ->addColumn('total_sum', function($app){
                $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
                $application = Task::where('category_id', $cat)->pluck('budget')->toArray();
                return array_sum($application);
            })->make(true);

    }

            public function child_report($id) {
                $query = Category::where('parent_id',$id)->query();
                return Datatables::of($query)
//                    ->addColumn('open_count', function($id){
//                        $cat = Category::where('parent_id', $id)->pluck('id')->toarray();
//                        $application = Task::where('category_id', $cat)->where('status', 1)->get();
//                        return count($application);
//                    })
//                    ->addColumn('open_sum', function($app){
//                        $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
//                        $application = Task::where('category_id', $cat)->where('status', 1)->pluck('budget')->toArray();
//                        return array_sum($application);
//                    })
//
//                    ->addColumn('process_count', function($app){
//                        $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
//                        $application = Task::where('category_id', $cat)->where('status', 3)->get();
//                        return count($application);
//                    })
//                    ->addColumn('process_sum', function($app){
//                        $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
//                        $application = Task::where('category_id', $cat)->where('status', 3)->pluck('budget')->toArray();
//                        return array_sum($application);
//                    })
//
//                    ->addColumn('finished_count', function($app){
//                        $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
//                        $application = Task::where('category_id', $cat)->where('status', 4)->get();
//                        return count($application);
//                    })
//                    ->addColumn('finished_sum', function($app){
//                        $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
//                        $application = Task::where('category_id', $cat)->where('status', 4)->pluck('budget')->toArray();
//                        return array_sum($application);
//                    })
//
//                    ->addColumn('open_count', function($app){
//                        $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
//                        $application = Task::where('category_id', $cat)->where('status', 1)->get();
//                        return count($application);
//                    })
//                    ->addColumn('open_sum', function($app){
//                        $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
//                        $application = Task::where('category_id', $cat)->where('status', 1)->pluck('budget')->toArray();
//                        return array_sum($application);
//                    })
//
//                    ->addColumn('total_count', function($app){
//                        $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
//                        $application = Task::where('category_id', $cat)->get();
//                        return count($application);
//                    })
//                    ->addColumn('total_sum', function($app){
//                        $cat = Category::where('parent_id', $app->id)->pluck('id')->toarray();
//                        $application = Task::where('category_id', $cat)->pluck('budget')->toArray();
//                        return array_sum($application);
//                    })
                    ->make(true);
                }

}
