<?php

namespace App\Http\Controllers;

use App\Models\WalletBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use TCG\Voyager\Models\Category;
use App\Models\User;
use App\Models\BrowsingHistory;
use App\Models\PostView;
use App\Models\UserView;
use Session;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Task;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;


class PerformersController extends Controller
{
    public  function performer_chat($id){

        $wallet1 = WalletBalance::where('user_id',$id)->first();
        $wallet2 = WalletBalance::where('user_id',auth()->user()->id)->first();
        if ($wallet1 == null || $wallet2==null){
            return redirect()->back();
        }
        if ($wallet1->balance>=4000 and $wallet2->balance>=4000 ){
            return redirect()->route('chat',['id'=>$id]);
        }else{
            return redirect()->back();
        }



    }
    public function service(){
        $categories = DB::table('categories')->get();
        $child_categories= DB::table('categories')->get();
        $users= User::where('role_id',2)->paginate(50);
        return view('Performers/performers',compact('child_categories','categories','users'));
    }
    public function performer($id){

        if(session('view_count') == NULL){

            $def_count = UserView::where('user_id', $id)->first();

            if(isset($def_count)){

            $ppi = $def_count->count + 1;

                UserView::where('user_id', $id)->update(['count' => $ppi]);

        }else{

            UserView::create([
                'user_id'=> $id,
                'count'=> 1,
            ]);

        }
        session()->put('view_count', '1');
        }
        $vcs = UserView::where('user_id', $id)->get();
        $users= User::where('id',$id)->get();
        $categories = DB::table('categories')->get();
        $child_categories = DB::table('categories')->get();
        return view('Performers/executors-courier',compact('users','categories','child_categories','vcs'));
    }

public function perf_ajax($cf_id){
    // $str = "1,2,3,4,5,6,7,8";
    // $cat_arr = explode(",",$str);
    //dd(array_search($id,$cat_arr));

    // $users = User::where('role_id',2)->paginate(50);

    // return $users;

    $categories = DB::table('categories')->get();
    $cur_cat = DB::table('categories')->where('id',$cf_id)->get();
    $child_categories= DB::table('categories')->get();
    $users= User::where('role_id',2)->paginate(50);

    return view('Performers/performers_cat',compact('child_categories','categories','users','cf_id','cur_cat'));

}

public function del_notif($id,$task_id){
    $balance = WalletBalance::where('user_id',Auth::id())->first();
    if ($balance){
        $balance =  $balance->balance;
    }else{
        $balance = 0;
    }
    Notification::where('id',$id)->delete();

    $tasks = Task::where('id',$task_id)->first();
    $cat_id = $tasks->category_id;
    $user_id = $tasks->user_id;
  $same_tasks = Task::where('category_id',$cat_id)->get();

  $users = User::all();
  $current_user = User::find($user_id);
  $categories = Category::where('id',$cat_id)->get();
  // dd($current_user);
  return view('task.detailed-tasks',compact('tasks','same_tasks','users','categories','current_user','balance'));
}

}
