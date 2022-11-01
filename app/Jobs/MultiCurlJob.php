<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Throwable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\{Page, CrawlerRequest, Resource};
use App\Library\Crawler;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

class MultiCurlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private array $urls)
    {
    }


    public function handle(): array
    {
        $urls = $this->urls;
        $responses = Http::pool(function(Pool $pool) use ($urls) {
           foreach ($urls as $url) {
               $pool->get(Crawler::ROOT_URL . $url['path']);
           }
        });

        return $responses;
    }

    // public function failed(\Throwable $exception)
    // {
    //     \dump('from failed: ' . $exception->getMessage());
    // }
}