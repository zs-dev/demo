<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use App\Library\Crawler;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

class MultiCurlJob
{
    use Dispatchable;

    /**
     * MultiCurlJob constructor.
     *
     * @param array $urls
     */
    public function __construct(private array $urls)
    {
    }

    /**
     * Multi curl calls.
     *
     * @return array
     */
    public function handle(): array
    {
        $urls = $this->urls;
        $responses = Http::pool(function (Pool $pool) use ($urls) {
            foreach ($urls as $url) {
                $pool->get(Crawler::ROOT_URL . $url['path']);
            }
        });

        return $responses;
    }
}
