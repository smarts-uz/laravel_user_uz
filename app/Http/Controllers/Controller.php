<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
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
        $tasks = $user->tasks()->orderBy('created_at', 'desc')->get();
        $perform_tasks = $user->performer_tasks;
        $datas = new Collection();
        $datas = $datas->merge($tasks);
        $datas = $datas->merge($perform_tasks);
        $categories = Category::where('parent_id', null)->select('id', 'name', 'slug')->get();
        $categories2 = Category::where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->get();

        return view('task.mytasks', compact('tasks', 'perform_tasks', 'categories', 'categories2', 'datas'));

    }

    public function category($id)
    {
        $service = new ControllerService();
        $item = $service->category($id);
        return view('task/choosetasks',
            [
                'child_categories' => $item->child_categories,
                'categories' => $item->categories,
                'choosed_category' => $item->choosed_category,
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
        $fileName = 'Правила_сервиса.pdf';
        return response()->download($filePath, $fileName, $headers);
    }

    public function index()
    {
        $medias = Massmedia::paginate(20);
        return view('reviews.CMI', compact('medias'));
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

    public function routing($request)
    {
        $routeName = $request->route;
        $category = \App\Models\Category::find($request->category_id);
        $data = [];

        switch ($routeName) {
            case CustomField::ROUTE_NAME:
                $data['custom_fields'] = $category->customFieldsInName;
                if ($category->parent->remote)
                    $data['next_route'] = CustomField::ROUTE_REMOTE;
                else
                    if (count($category->customFieldsInCustom))
                        $data['next_route'] = CustomField::ROUTE_CUSTOM;
                    else
                        $data['next_route'] = CustomField::ROUTE_ADDRESS;
                break;
            case CustomField::ROUTE_REMOTE:
                $data['custom_fields'] = [];
                if ($category->parent->remote)
                    if (count($category->customFieldsInCustom))
                        $data['next_route'] = CustomField::ROUTE_CUSTOM;
                    else
                        $data['next_route'] = CustomField::ROUTE_ADDRESS;
                else
                    $data['next_route'] = CustomField::ROUTE_ADDRESS;
                break;
            case CustomField::ROUTE_ADDRESS:
                $data['custom_fields'] = $category->customFieldsInAddress;
                $data['next_route'] = CustomField::ROUTE_CUSTOM;
                break;
            case CustomField::ROUTE_CUSTOM:
                $data['custom_fields'] = $category->customFieldsInCustom;
                $data['next_route'] = CustomField::ROUTE_DATE;
                break;
            case CustomField::ROUTE_DATE:
                $data['custom_fields'] = $category->customFieldsInDate;
                $data['next_route'] = CustomField::ROUTE_BUDGET;
                break;
            case CustomField::ROUTE_BUDGET:
                $data['custom_fields'] = $category->customFieldsInBudget;
                $data['next_route'] = CustomField::ROUTE_NOTE;
                break;
            case CustomField::ROUTE_NOTE:
                $data['custom_fields'] = $category->customFieldsInNote;
                $data['next_route'] = CustomField::ROUTE_CONTACTS;
                break;
            case CustomField::ROUTE_CONTACTS:
                $data['custom_fields'] = $category->customFieldsInNote;
                break;

        }

        return $data;

    }


    private function validate($route)
    {

    }

}
