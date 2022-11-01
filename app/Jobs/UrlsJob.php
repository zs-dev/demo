<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Resource;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use App\Library\Crawler;

class UrlsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private $slashPath, private $pageId, private $numberOfPages)
    {
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
        $responses = Http::pool(function (Pool $pool) use ($urls) {
            foreach ($urls as $url) {
                $pool->get(Crawler::ROOT_URL . $url['path']);
            }
        });

        return  $responses;
    }
}
