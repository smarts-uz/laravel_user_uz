<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
use TCG\Voyager\Models\Category;
use App\Models\Massmedia;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function home(Request $request)
    {
        $categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', null)->get();
        $tasks  =  Task::where('status', 1)->orWhere('status',2)->orderBy('id', 'desc')->take(20)->get();
        return view('home', compact('tasks', 'categories'));
    }
    public function index()
    {
        $medias = Massmedia::paginate(20);
        return view('reviews.CMI',compact('medias'));
    }
    public function my_tasks()
    {
        $user = auth()->user();
        $tasks = $user->tasks()->orderBy('created_at','desc')->get();
        $perform_tasks = $user->performer_tasks;
        $datas = new Collection(); 
        $datas = $datas->merge($tasks);
        $datas = $datas->merge($perform_tasks);
        $categories = Category::where('parent_id', null)->select('id','name','slug')->get();
        $categories2 = Category::where('parent_id','<>', null)->select('id','parent_id','name')->get();

        return view('task.mytasks',compact('tasks','perform_tasks','categories','categories2','datas'));

    }

    public function category($id)
    {
        $categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', null)->get();
        $choosed_category = Category::withTranslations(['ru', 'uz'])->where('id', $id)->get();
        $child_categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', $id)->get();
        $idR = $id;
        return view('task/choosetasks', compact('child_categories', 'categories', 'choosed_category', 'idR'));
    }
    public function lang($lang)
    {
        Session::put('lang', $lang);
        return redirect()->back();
    }
    public function download()
    {
        $filePath = $filePath = public_path("Правила_сервиса.pdf");
        $headers = ['Content-Type: application/pdf'];
        $fileName ='Правила_сервиса.pdf';
        return response()->download($filePath, $fileName, $headers);
    }
    public function geotaskshint()
    {
        return view('/staticpages/geotaskshint');
    }
    public function security()
    {
        return view('/staticpages/security');
    }
    public function badges()
    {
        return view('/staticpages/badges');
    }
}
