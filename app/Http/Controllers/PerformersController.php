<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\WalletBalance;
use App\Services\NotificationService;
use App\Services\PerformersService;
use App\Services\SmsTextService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Portfolio;
use App\Models\User;
use App\Models\Task;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;


class PerformersController extends Controller
{


    public function performer_chat($id)
    {

        $wallet1 = WalletBalance::where('user_id', $id)->first();
        $wallet2 = WalletBalance::where('user_id', auth()->user()->id)->first();
        if ($wallet1 == null || $wallet2 == null) {
            return redirect()->back();
        }
        if ($wallet1->balance >= 4000 and $wallet2->balance >= 4000) {
            return redirect()->route('chat', ['id' => $id]);
        } else {
            return redirect()->back();
        }
    }


    /**
     *
     * Function  service
     * @param User $user
     * @param Request $request
     * @return  \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function service(User $user, Request $request)
    {
        $authId = Auth::id();

        $service = new PerformersService();
        $item = $service->service($authId, $user);

       return view('performers/performers',
           [
                'users' => $item->users,
                'tasks' => $item->tasks,
                'top_users' => $item->top_users,
                'categories' => $item->categories,
                'categories2' => $item->categories2,
           ]);
    }


    public function performer(User $user, Request $request)
    {
        setview($user);

        $service = new PerformersService();
        $item = $service->performer($user);

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
            ]);
    }


    public function give_task(Request $request)
    {
        if ($request->input('user_id') != null) {
            $request->session()->put('given_id', $request->input('user_id'));
        }
        $task_id = $request->input('task_id');

        if (isset($task_id)) {
            $task_name = Task::where('id', $task_id)->first();
            $users_id = $request->session()->pull('given_id');
            $performer = User::query()->find($users_id);
            $text_url = route("searchTask.task",$task_id);
            $text = "Заказчик предложил вам новую задания $text_url. Имя заказчика: " . $task_name->user->name;
            $phone_number=$performer->phone_number;;
            $sms_service = new SmsTextService();
            $sms_service->sms_packages($phone_number, $text);
            $notification = Notification::create([
                'user_id' => $task_name->user_id,
                'performer_id' => $users_id,
                'task_id' => $task_id,
                'name_task' => $task_name->name,
                'description' => '123',
                'type' => Notification::GIVE_TASK,
            ]);

            NotificationService::sendNotificationRequest([$users_id], [
                'url' => 'detailed-tasks' . '/' . $task_id, 'name' => $task_name->name, 'time' => 'recently'
            ]);
            NotificationService::pushNotification($performer->firebase_token, [
                'title' => __('Предложение'), 'body' => __('Вам предложили новое задание task_name №task_id от заказчика task_user', [
                    'task_name' => $notification->name_task, 'task_id' => $notification->task_id, 'task_user' => $notification->user?->name
                ])
            ], 'notification', new NotificationResource($notification));

            return response()->json(['success' => $users_id]);
        }
        return response()->json(['success' => '$users_id']);
    }


    public function perf_ajax($cf_id, User $user)
    {
        $service = new PerformersService();
        $item = $service->perf_ajax($cf_id, $user);
        return view('performers/performers_cat',
        [
            'child_categories' => $item->child_categories,
            'top_users' => $item->top_users,
            'user' => $user,
            'categories' => $item->categories,
            'categories2' => $item->categories2,
            'users' =>$item->users,
            'cur_cat' =>$item->cur_cat,
            'tasks' =>$item->tasks,
            'cf_id' => $cf_id,
        ]);

    }

    public function ajaxAP()
    {
        $date = Carbon::now()->subMinutes(2)->toDateTimeString();
        $activePerformers = User::where([['role_id', 2], ['last_seen', ">=", $date]])
            ->select('id')
            ->get();

        return $activePerformers->all();
    }

    public function deleteNotification(Notification $notification)
    {
        $notification->delete();
        return redirect()->route('searchTask.task', $notification->task_id);
    }

    public function del_all_notif()
    {
        Notification::where('user_id', Auth::id())->delete();
        return response()->json(['success']);
    }

    public function performers_portfolio(User $user,Portfolio $portfolio){

        return view('performers.performer_portfolio',compact('portfolio','user'));
    }
}
