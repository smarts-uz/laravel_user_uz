<?php

namespace App\Http\Controllers;

use App\Models\Response;
use App\Models\WalletBalance;
use App\Models\Review;
use App\Services\NotificationService;
use App\Services\PerformersService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use TCG\Voyager\Models\Category;
use App\Models\User;
use App\Models\Session;
use App\Models\BrowsingHistory;
use App\Models\PostView;
use App\Models\UserView;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Task;
use App\Models\Notification;
use App\Events\MyEvent;
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
        // $str = "1,2,3,4,5,6,7,8";
        // $cat_arr = explode(",",$str);
        //dd(array_search($id,$cat_arr));

        // $users = User::where('role_id',2)->paginate(50);

        // return $users;
        $about = User::where('role_id', 2)->orderBy('reviews', 'desc')->take(20)->get();
        $task_count = $user->performer_tasks_count;
        $categories = Category::get();
        $cur_cat = Category::where('id', $cf_id)->get();
        $child_categories = Category::get();
        $users = User::where('role_id', 2)->paginate(50);
        $tasks = Task::where('user_id', Auth::id())->get();

        return view('performers/performers_cat', compact('child_categories', 'about', 'user', 'task_count', 'categories', 'users', 'cf_id', 'cur_cat', 'tasks'));

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

    public function user_online(Request $request)
    {
        $users = User::select("*")
            ->whereNotNull('last_seen')
            ->orderBy('last_seen', 'DESC');

        return view('Performers.performers', compact('users'));
    }
}
