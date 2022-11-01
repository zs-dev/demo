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
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

class MultiCurlJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private array $urls)
    {
    }

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
