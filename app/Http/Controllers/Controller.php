<?php

namespace App\Http\Controllers;

use App\Models\{Category, CustomField, FooterReview, Massmedia, BlogNew, Privacy};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Services\{Response, ControllerService};
use Illuminate\Foundation\{Auth\Access\AuthorizesRequests, Bus\DispatchesJobs, Validation\ValidatesRequests};
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\{Redirect, Session};
use Jenssegers\Agent\Agent;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use Response;

    public function home(): Factory|View|Application
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

    public function my_tasks(): Factory|View|Application
    {
        $lang = Session::get('lang');
        $userId = auth()->id();
        $service = new ControllerService();
        $item = $service->my_tasks($userId, $lang);
        return view('task.mytasks',
        [
            'categories' => $item->categories,
            'categories2' => $item->categories2,
            'perform_tasks' => $item->perform_tasks,
            'tasks' => $item->tasks,
        ]);

    }

    public function category($id): Factory|View|Application
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

    public function lang($lang): RedirectResponse
    {
        Session::put('lang', $lang);
        if (auth()->check()) {
            app()->setLocale($lang);
            cache()->put('lang' . auth()->id(), $lang);
        }
        return redirect()->back();
    }

    public function massmedia(): Factory|View|Application
    {
        $medias = Massmedia::paginate(20);
        return view('reviews.CMI', compact('medias'));
    }

    public function performer_reviews(): Factory|View|Application
    {
        $performer_reviews = FooterReview::where('review_type',2)->latest()->get();
        return view('reviews.review',compact('performer_reviews'));
    }

    public function authors_reviews(): Factory|View|Application
    {
        $customer_reviews = FooterReview::where('review_type',1)->latest()->get();
        return view('reviews.authors_reviews',compact('customer_reviews'));
    }

    public function geotaskshint(): Factory|View|Application
    {
        return view('/staticpages/geotaskshint');
    }

    public function security(): Factory|View|Application
    {
        return view('/staticpages/security');
    }

    public function badges(): Factory|View|Application
    {
        return view('/staticpages/badges');
    }

    public function news(): Factory|View|Application
    {
        $news = BlogNew::latest()->get();
        return view('/staticpages/news',compact('news'));
    }

    public function news_page(BlogNew $id): Factory|View|Application
    {
        $news = BlogNew::find($id);
        return view('staticpages.blog_new', compact('news'));
    }

    public function policy(): Factory|View|Application
    {
        $policies = Privacy::get();
        return view('/staticpages/privacy',compact('policies'));
    }

    public function terms(): Factory|View|Application
    {
        $agent = new Agent();
        if ($agent->isMobile()) {
            return view('auth.terms_mobile');
        }

        return view('auth.terms');
    }

    public function terms_mobile($lang): RedirectResponse
    {
        Session::put('lang', $lang);
        if (auth()->check()) {
            app()->setLocale($lang);
            cache()->put('lang' . auth()->id(), $lang);
        }
        return redirect()->route('terms_mobile_url');
    }

    public function paynet_mobile($lang): RedirectResponse
    {
        Session::put('lang', $lang);
        if (auth()->check()) {
            app()->setLocale($lang);
            cache()->put('lang' . auth()->id(), $lang);
        }
        return redirect()->route('paynet_oplata_url');
    }

    public function paynet_oplata(): Factory|View|Application
    {
        $agent = new Agent();
        if ($agent->isMobile()) {
            return view('staticpages.paynet_mobile');
        }

        return view('staticpages.paynet');
    }

    public function user_info($user): Factory|View|Application
    {

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

    public function device(): RedirectResponse
    {
        $agent = new Agent();
        if ($agent->isAndroidOS()) {
            return Redirect::away(setting('site.android_url','https://play.google.com/store/apps/details?id=uz.smart.useruz'));
        }

        if($agent->isIos()) {
            return Redirect::away(setting('site.ios_url','https://apps.apple.com/app/useruz/id1645713842'));
        }

        return Redirect::away(setting('site.android_url','https://play.google.com/store/apps/details?id=uz.smart.useruz'));
    }

    public function routing($request)
    {
        $routeName = $request->route;
        $category = Category::find($request->category_id);
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
