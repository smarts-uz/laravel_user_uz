<?php

namespace App\Http\Controllers;

use App\Services\PerformersService;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Portfolio;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class PerformersController extends Controller
{

    protected PerformersService $performerService;

    public function __construct()
    {
        $this->performerService = new PerformersService();
    }
    public function service(Request $request): Factory|View|Application
    {
        $lang = Session::get('lang');
        $search = $request->input('search');
        $authId = Auth::id();
        $item = $this->performerService->service($authId,$search,$lang);

       return view('performers/performers',
           [
                'users' => $item->users,
                'tasks' => $item->tasks,
                'top_users' => $item->top_users,
                'categories' => $item->categories,
                'categories2' => $item->categories2,
           ]);
    }

    public function performer(User $user): Factory|View|Application
    {
        (new PerformersService)->setView($user);
        $item = $this->performerService->performer($user);

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
                'created' => $item->created,
                'user_category'=>$item->user_category
            ]);
    }


    public function give_task(Request $request): JsonResponse
    {

        $task_id = $request->input('task_id');
        $user_id = $request->input('user_id');
        $session = $request->session();

        $this->performerService->task_give_web($task_id, $user_id, $session);

        return response()->json(['success' => true]);
    }


    public function perf_ajax($cf_id, User $user, Request $request): Factory|View|Application
    {
        $authId = Auth::id();
        $search = $request->input('search');

        $item = $this->performerService->perf_ajax($authId,$search,$cf_id);
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

    /* public function getPerformers(): JsonResponse
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
  }*/

}
