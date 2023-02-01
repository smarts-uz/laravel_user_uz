<?php

namespace App\Providers;

use App\Http\Resources\BlogNewsResource;
use App\Models\BlogNew;
use App\Models\Review;
use App\Observers\ReviewObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
	    Review::observe(ReviewObserver::class);
        Voyager::addAction(\App\Actions\ActiveAction::class);
        Voyager::addAction(\App\Actions\CancelAction::class);
        Voyager::addAction(\App\Actions\InfoAction::class);
        Voyager::addAction(\App\Actions\PasswordResetAction::class);
        foreach (glob(__DIR__.'/../Helpers/*.php') as $filename) {
            require_once $filename;
        }
        Paginator::useBootstrap();
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $view->with('news', BlogNewsResource::collection(BlogNew::query()->latest()->get()));
            }
        });

    }
}
