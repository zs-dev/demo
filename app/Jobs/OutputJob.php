<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Page;
use Illuminate\Support\Str;

class OutputJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * OutputJob constructor.
     *
     * @param int $crawlerId
     */
    public function __construct(private int $crawlerId)
    {
    }

    /**
     * Create data from crawling.
     *
     * @return array
     */
    public function handle(): array
    {
        $pages = Page::where('crawler_request_id', $this->crawlerId)->with('resources')->get();

        $imagePaths = [];
        $internalPaths = [];
        $externalPaths = [];
        $times = [];
        $wordCount = [];
        $titleCount = [];

        foreach ($pages as $page) {
            foreach ($page->resources as $resource) {
                if ($resource->resource === 'image') {
                    $imagePaths[] = $resource->path;
                } elseif ($resource->resource === 'internal_link') {
                    $internalPaths[] = $resource->path;
                } elseif ($resource->resource === 'external_link') {
                    $externalPaths[] = $resource->path;
                }
            }
            $times[] = $page->load_time;
            $wordCount[] = $page->word_count;
            $titleCount[] = Str::length($page->title);
        }

        return [
            'number_pages_crawled' => $pages->count(),
            'number_unique_images' => count(array_unique($imagePaths)),
            'number_unique_internal_links' => count(array_unique($internalPaths)),
            'number_unique_external_links' => count(array_unique($externalPaths)),
            'average_page_load_in_seconds' => (array_sum($times) / $pages->count()),
            'average_word_count' => (array_sum($wordCount) / $pages->count()),
            'average_title_length' => (array_sum($titleCount) / $pages->count()),
            'pages' => $pages,
            'index' => config('app.url')

        ];
    }
}
