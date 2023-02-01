<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Review;
use App\Services\NotificationService;
use App\Services\PerformersService;
use App\Services\SmsMobileService;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Portfolio;
use App\Models\User;
use App\Models\Task;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;


class PerformersController extends Controller
{

    protected PerformersService $performerService;

    public function __construct()
    {
        $this->performerService = new PerformersService();
    }
    public function service(Request $request): Factory|View|Application
    {
        $search = $request->input('search');
        $authId = Auth::id();
        $service = new PerformersService();
        $item = $service->service($authId,$search);

       return view('performers/performers',
           [
                'users' => $item->users,
                'tasks' => $item->tasks,
                'top_users' => $item->top_users,
                'categories' => $item->categories,
                'categories2' => $item->categories2,
           ]);
    }


    public function getPerformers(): JsonResponse
    {

            $data = User::query()
                ->where('role_id', User::ROLE_PERFORMER)
                ->WhereNot('id',\auth()->id())
                ->orderByDesc('review_rating')
                ->orderbyRaw('(review_good - review_bad) DESC')->get();

            return Datatables::of($data)
                ->addColumn('user_images', function (User $user) {
                    return view('performers.user_images',[
                        'user' => $user
                    ]);
                })
                ->addColumn('user_information', function (User $user) {
                    $top_users = Cache::get('users')
                        ->where('review_rating', '!=', 0)
                        ->where('role_id', User::ROLE_PERFORMER)
                        ->sortBy('(review_good - review_bad) DESC')
                        ->take(Review::TOP_USER)
                        ->pluck('id')
                        ->toArray();
                    $authId = Auth::id();
                    $tasks = Cache::get('tasks')->where('user_id', $authId)
                        ->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE])->sortBy('created_at DESC');
                    return view('performers.user_information',[
                        'user' => $user,
                        'top_users' => $top_users,
                        'tasks' => $tasks,
                    ]);
                })
                ->make(true);
    }


    public function performer(User $user): Factory|View|Application
    {
        setview($user);

        $service = new PerformersService();
        $item = $service->performer($user);

        $value = Carbon::parse($user->created_at)->locale(getLocale());
        $day = $value == now()->toDateTimeString() ? "Bugun" : "$value->day-$value->monthName";
        $created = "$day  $value->year";

        return view('performers/executors-courier',
            [
                'top_users' => $item->top_users,
                'user' => $user,
                'portfolios' => $item->portfolios,
                'goodReviews' => $item->goodReviews,
                'badReviews' => $item->badReviews,
                'review_good' => $item->review_good,
                'review_bad' =>$item->review_bad,
                'review_rating' => $item->review_rating,
                'task_count' => $item->task_count,
                'created' => $created,
                'user_category'=>$item->user_category
            ]);
    }


    public function give_task(Request $request): JsonResponse
    {

        $task_id = $request->input('task_id');
        $user_id = $request->input('user_id');

        $this->performerService->task_give($task_id, $user_id, $request);

        return response()->json(['success' => true]);
    }


    public function perf_ajax($cf_id, User $user, Request $request): Factory|View|Application
    {
        $authId = Auth::id();
        $search = $request->input('search');
        $service = new PerformersService();
        $item = $service->perf_ajax($authId,$search,$cf_id);
        return view('performers/performers_cat',
        [
            'top_users' => $item->top_users,
            'user' => $user,
            'categories' => $item->categories,
            'categories2' => $item->categories2,
            'users' =>$item->users,
            'tasks' =>$item->tasks,
            'cf_id' => $cf_id,
        ]);

    }

    public function ajaxAP(): array
    {
        $date = Carbon::now()->subMinutes(2)->toDateTimeString();
        $activePerformers = User::query()->where([['role_id', User::ROLE_PERFORMER], ['last_seen', ">=", $date]])
            ->select('id')
            ->get();

        return $activePerformers->all();
    }

    public function del_all_notif(): JsonResponse
    {
        Notification::query()->where('user_id', Auth::id())->delete();
        return response()->json(['success']);
    }

    public function performers_portfolio(User $user,Portfolio $portfolio): Factory|View|Application
    {
        return view('performers.performer_portfolio',
        [
            'user' => $user,
            'portfolio' => $portfolio
        ]);
    }

}
