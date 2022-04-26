<?php

namespace App\Http\Controllers;

use App\Models\WalletBalance;
use App\Services\NotificationService;
use App\Services\PerformersService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use TCG\Voyager\Models\Category;
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
                'about' => $item->about,
                'task_count' => $item->task_count,
                'categories' => $item->categories,
                'categories2' => $item->categories2,
                'user_online' => $item ->user_online,
            ]);
    }


    public function performer(User $user, Request $request)
    {
        setview($user);

        $service = new PerformersService();
        $item = $service->performer($user);

        $goodReviews = $user->goodReviews()->whereHas('task')->whereHas('user')->get();
        $badReviews = $user->badReviews()->whereHas('task')->whereHas('user')->get();

        return view('performers/executors-courier',
            [
                'reviews' => $item->reviews,
                'about' => $item->about,
                'user' => $user,
                'task_count' => $item->task_count,
                'portfolios' => $item->portfolios,
                'goodReviews' => $goodReviews,
                'badReviews' => $badReviews,
            ]);
    }


    public function give_task(Request $request)
    {
        if ($request->input('user_id') != null) {
            $request->session()->put('given_id', $request->input('user_id'));
        }

        $task_id = $request->input('task_id');
        $task_name = Task::where('id', $task_id)->first();
        if (isset($task_id)) {
            $users_id = $request->session()->pull('given_id');
            $notification = Notification::create([
                'user_id' => $users_id,
                'task_id' => $task_id,
                'name_task' => $task_name->name,
                'description' => '123',
                'type' => 4,
            ]);

            NotificationService::sendNotificationRequest([$users_id], [
                'url' => 'detailed-tasks' . '/' . $task_id, 'name' => $task_name->name, 'time' => 'recently'
            ]);

            return response()->json(['success' => $users_id]);
        }
        return response()->json(['success' => '$users_id']);
    }


    public function perf_ajax($cf_id, User $user)
    {
        $task_count = $user->performer_tasks_count;
        $service = new PerformersService();
        $item = $service->perf_ajax($cf_id, $user);
        return view('performers/performers_cat',
        [
            'child_categories' => $item->child_categories,
            'about' => $item->about,
            'user' => $user,
            'task_count' => $task_count,
            'categories' => $item->categories,
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
}
