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

class CreateCrawlerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private array $responses, private array $urls, private Crawler $crawler)
    {
    }


    public function handle(): void
    {
        foreach ($this->responses as $key => $response) {
            // $crawler = app()->makeWith(
            //     Crawler::class,
            //     ['response' => $response, 'path' => $this->urls[$key]['path'], 'crawlerRequest' => $this->crawler->getCrawlerRequest()]
            // );
            $this->crawler->setResponse($response);
            $this->crawler->setXPath($response);
            $this->crawler->setPath($this->urls[$key]['path']);
            $this->crawler->savePage();
            $this->crawler->saveResources();
        }
    }
}
