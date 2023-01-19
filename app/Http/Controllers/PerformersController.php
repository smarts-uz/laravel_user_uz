<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Review;
use App\Services\NotificationService;
use App\Services\PerformersService;
use App\Services\SmsMobileService;
use Carbon\Carbon;
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
    public function service(Request $request)
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

    public function getPerformers_Old1(Request $request)
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

 public function getPerformers(Request $request)
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


    public function performer(User $user)
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


    public function give_task(Request $request)
    {
        if ($request->input('user_id') !== null) {
            $request->session()->put('given_id', $request->input('user_id'));
        }
        $task_id = $request->input('task_id');

        if (isset($task_id)) {
            /** @var Task $task_name */
            $task_name = Task::query()->where('id', $task_id)->first();
            $users_id = $request->session()->pull('given_id');
            /** @var User $performer */
            $performer = User::query()->find($users_id);
            $text_url = route("searchTask.task",$task_id);
            $message = __('Вам предложили новое задание task_name №task_id от заказчика task_user', [
                'task_name' => $text_url, 'task_id' => $task_id, 'task_user' => $task_name->user?->name
            ]);
            $phone_number=$performer->phone_number;
            SmsMobileService::sms_packages(correctPhoneNumber($phone_number), $message);
            /** @var Notification $notification */
            $notification = Notification::query()->create([
                'user_id' => $task_name->user_id,
                'performer_id' => $users_id,
                'task_id' => $task_id,
                'name_task' => $task_name->name,
                'description' => '123',
                'type' => Notification::GIVE_TASK,
            ]);

            NotificationService::sendNotificationRequest([$users_id], [
                'created_date' => $notification->created_at->format('d M'),
                'title' => NotificationService::titles($notification->type),
                'url' => route('show_notification', [$notification]),
                'description' => NotificationService::descriptions($notification)
            ]);
//            NotificationService::sendNotificationRequest([$users_id], [
//                'url' => 'detailed-tasks' . '/' . $task_id, 'name' => $task_name->name, 'time' => 'recently'
//            ]);
            $locale = cacheLang($performer->id);
            NotificationService::pushNotification($performer, [
                'title' => __('Предложение', [], $locale), 'body' => __('Вам предложили новое задание task_name №task_id от заказчика task_user', [
                    'task_name' => $notification->name_task, 'task_id' => $notification->task_id, 'task_user' => $notification->user?->name
                ], $locale)
            ], 'notification', new NotificationResource($notification));

            return response()->json(['success' => $users_id]);
        }
        return response()->json(['success' => '$users_id']);
    }


    public function perf_ajax($cf_id, User $user, Request $request)
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

    public function ajaxAP()
    {
        $date = Carbon::now()->subMinutes(2)->toDateTimeString();
        $activePerformers = User::query()->where([['role_id', User::ROLE_PERFORMER], ['last_seen', ">=", $date]])
            ->select('id')
            ->get();

        return $activePerformers->all();
    }

    public function del_all_notif()
    {
        Notification::query()->where('user_id', Auth::id())->delete();
        return response()->json(['success']);
    }

    public function performers_portfolio(User $user,Portfolio $portfolio){
        return view('performers.performer_portfolio',
        [
            'user' => $user,
            'portfolio' => $portfolio
        ]);
    }

}
