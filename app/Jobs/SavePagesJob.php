<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use App\Library\Crawler;

class SavePagesJob
{
    use Dispatchable;

    /**
     * SavePagesJob constructor.
     *
     * @param array $responses
     * @param array $urls
     * @param Crawler $crawler
     */
    public function __construct(private array $responses, private array $urls, private Crawler $crawler)
    {
    }

    /**
     * Save data from multi curl call;
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->responses as $key => $response) {
            $this->crawler->setResponse($response);
            $this->crawler->setXPath($response);
            $this->crawler->setPath($this->urls[$key]['path']);
            $this->crawler->savePage();
            $this->crawler->saveResources();
        }
    }
}
