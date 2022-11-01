<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\CrawlerDbActions;
use App\Models\CrawlerRequest;
use App\Library\Crawler;

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
        $this->app->bind(CrawlerDbActions::class, function ($app, $params) {
            return new CrawlerDbActions();
        });

        $this->app->bind(Crawler::class, function ($app, $params) {
            return new Crawler(
                $params['response'],
                $params['path'],
                CrawlerRequest::create([]),
                $this->app->make(CrawlerDbActions::class)
            );
        });
    }
}
