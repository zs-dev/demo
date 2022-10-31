<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\CrawlerDbActions;
use App\Models\CrawlerRequest;
use App\Library\Crawler;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

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
    DB::listen(function($query) {
        File::append(
            storage_path('/logs/query.log'),
            $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL
       );
    });
        $this->app->bind(CrawlerDbActions::class, function ($app, $params) {
            return new CrawlerDbActions();
        });

        $this->app->bind(Crawler::class, function ($app, $params) {
            $crawlerRequest = (!empty($params['crawlerRequest'])) ? $params['crawlerRequest'] : CrawlerRequest::create([]);

            return new Crawler($params['response'], $params['path'], $crawlerRequest, $this->app->make(CrawlerDbActions::class));
        });
    }
}
