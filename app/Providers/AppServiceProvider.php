<?php

namespace App\Providers;

use App\Models\Review;
use App\Observers\ReviewObserver;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

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
        Voyager::addAction(\App\Actions\NewsNotifAction::class);
        Voyager::addAction(\App\Actions\ChatAction::class);
        foreach (glob(__DIR__.'/../Helpers/*.php') as $filename) {
            require_once $filename;
        }
        Paginator::useBootstrap();

        Queue::before(static function (JobProcessing $event) {
             $event->job->timeout();
             $event->job->getConnectionName();
             $event->job->payload();
        });

        Queue::after(static function (JobProcessed $event) {
            $event->job->timeout();
            $event->job->getConnectionName();
            $event->job->payload();
        });

    }
}
