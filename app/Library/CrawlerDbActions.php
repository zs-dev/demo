<?php

declare(strict_types=1);

namespace App\Library;

use Illuminate\Http\Client\Response;
use DOMXPath;
use App\Models\{Page, CrawlerRequest};
use Illuminate\Support\Facades\DB;

class CrawlerDbActions
{
    /**
     * Create page.
     *
     * @param string $path
     * @param Response $response
     * @param DOMXPath $xPath
     * @param CrawlerRequest $crawlerRequest
     *
     * @return Page
     */
    public function savePage(string $path, Response $response, DOMXPath $xPath, CrawlerRequest $crawlerRequest): Page
    {
        return Page::create([
            'path' => $path,
            'load_time' => $response->transferStats->getTransferTime(),
            'status' => $response->getStatusCode(),
            'title' => (!empty(trim($xPath->query('//title')->item(0)->textContent))) ? trim($xPath->query('//title')->item(0)->textContent) : null,
            'crawler_request_id' => $crawlerRequest->id,
            'word_count' => str_word_count($xPath->query('//html')->item(0)->textContent),
        ]);
    }

    /**
     * Mass insert for page's resouces.
     *
     * @param array $resources
     * @param Page $page
     * @param string $resourceSting
     *
     * @return void
     */
    public function saveResource(array $resources, Page $page, string $resourceSting)
    {
        $resources = collect($resources)->unique();
        foreach ($resources->chunk(500)->toArray() as $resourceArray) {
            $records = [];
            foreach ($resourceArray as $resource) {
                $records[] = ['page_id' => $page->id, 'path' => $resource, 'resource' => $resourceSting];
            }
            DB::table('resources')->insert($records);
        }
    }
}
