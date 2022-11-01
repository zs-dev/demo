<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Library\Crawler;

class SavePagesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private array $responses, private array $urls, private Crawler $crawler)
    {
    }


    public function handle(): void
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
