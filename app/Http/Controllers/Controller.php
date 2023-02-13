<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Models\FooterReview;
use App\Services\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
use App\Models\Massmedia;
use App\Services\ControllerService;
use App\Models\BlogNew;
use App\Models\Privacy;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use Response;

    public function home()
    {
        $lang = Session::get('lang');
        $service = new ControllerService();
        $item = $service->home($lang);
        return view('home',
            [
                'categories' => $item->categories,
                'tasks' => $item->tasks,
                'child_categories' => $item->child_categories,
            ]
        );
    }

    public function my_tasks()
    {
        $lang = Session::get('lang');
        $user = auth()->user();
        $service = new ControllerService();
        $item = $service->my_tasks($user, $lang);
        return view('task.mytasks',
        [
            'categories' => $item->categories,
            'categories2' => $item->categories2,
            'perform_tasks' => $item->perform_tasks,
            'tasks' => $item->tasks,
        ]);

    }

    public function category($id)
    {
        $lang = Session::get('lang');
        $service = new ControllerService();
        $item = $service->category($id, $lang);
        return view('task/choosetasks',
            [
                'child_categories' => $item->child_categories,
                'categories' => $item->categories,
                'choosed_category' => $item->choosed_category,
            ]);
    }

    public function lang($lang)
    {
        Session::put('lang', $lang);
        if (auth()->check()) {
            app()->setLocale($lang);
            cache()->put('lang' . auth()->id(), $lang);
        }
        return redirect()->back();
    }

    public function index()
    {
        $medias = Massmedia::paginate(20);
        return view('reviews.CMI', compact('medias'));
    }

    public function performer_reviews()
    {
        $performer_reviews = FooterReview::where('review_type',2)->latest()->get();
        return view('reviews.review',compact('performer_reviews'));
    }

    public function authors_reviews(){
        $customer_reviews = FooterReview::where('review_type',1)->latest()->get();
        return view('reviews.authors_reviews',compact('customer_reviews'));
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

    public function news()
    {
        $news = BlogNew::latest()->get();
        return view('/staticpages/news',compact('news'));
    }

    public function news_page(BlogNew $id)
    {
        $news = BlogNew::find($id);
        return view('staticpages.blog_new', compact('news'));
    }

    public function policy()
    {
        $policies = Privacy::get();
        return view('/staticpages/privacy',compact('policies'));
    }

    public function terms()
    {
        return view('auth.terms');
    }

    public function terms_mobile($lang)
    {
        Session::put('lang', $lang);
        if (auth()->check()) {
            app()->setLocale($lang);
            cache()->put('lang' . auth()->id(), $lang);
        }
        return view('auth.terms_mobile');
    }

    public function paynet_mobile($lang)
    {
        Session::put('lang', $lang);
        if (auth()->check()) {
            app()->setLocale($lang);
            cache()->put('lang' . auth()->id(), $lang);
        }
        return view('staticpages.paynet_mobile');
    }

    public function paynet_oplata()
    {
        return view('staticpages.paynet');
    }

    public function user_info($user){

        $service = new ControllerService();
        $item = $service->user_info($user);
        return view('user_managment.info',
            [
                'tasks' => $item->tasks,
                'performer_tasks' => $item->performer_tasks,
                'user_reviews' => $item->user_reviews,
                'performer_reviews' => $item->performer_reviews,
                'task_responses' => $item->task_responses,
            ]
        );
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

}
