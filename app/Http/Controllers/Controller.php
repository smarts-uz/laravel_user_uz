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
use App\Services\ControllerService;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function home()
    {
        $service = new ControllerService();
        $item = $service->home();
        return view('home',
        [
            'categories' => $item->categories,
            'tasks' => $item->tasks,
        ]
    );
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
        $service = new ControllerService();
        $item = $service->category($id);
        return view('task/choosetasks',
        [
            'child_categories' => $item->child_categories,
            'categories' => $item->categories,
            'choosed_category' => $item-> choosed_category,
            'idR' => $item->idR,
        ]);
    }
    public function lang($lang)
    {
        Session::put('lang', $lang);
        return redirect()->back();
    }
    public function download()
    {
        $filePath = public_path("Правила_сервиса.pdf");
        $headers = ['Content-Type: application/pdf'];
        $fileName ='Правила_сервиса.pdf';
        return response()->download($filePath, $fileName, $headers);
    }
    public function index()
    {
        $medias = Massmedia::paginate(20);
        return view('reviews.CMI',compact('medias'));
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
