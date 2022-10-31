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

class UrlsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private   $slashPath,
         private $pageId,
         private $numberOfPages,

    )
    {
       // $this->response = $response;
        // $doc = new DOMDocument();
        // $internalErrors = libxml_use_internal_errors(true);
        // $doc->loadHTML($this->response->getBody()->getContents());
        // libxml_use_internal_errors($internalErrors);
        // $this->xPath = new DOMXPath($doc);
        // $this->crawlerDbActions = app()->make(CrawlerDbActions::class);
    }


    public function handle(): array
    {
    $urls = Resource::whereNotIn('path', [$this->slashPath])
            ->where('resource', 'internal_link')
            ->where('page_id', $this->pageId)
            ->inRandomOrder()
            ->limit($this->numberOfPages)
            ->get(['path']);

    $urls = $urls->toArray();
    $responses = Http::pool(function(Pool $pool) use ($urls) {
       foreach ($urls as $url) {
           $pool->get('https://agencyanalytics.com/' . ltrim($url['path'], '/'));
       }
    });
    return  $responses;

    }

    public function failed(\Throwable $exception)
    {
        \dump('from failed: ' . $exception->getMessage());
    }
}