<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\{Page, CrawlerRequest, Resource};
use Illuminate\Support\Facades\Validator;
use App\Library\{CrawlerDbActions, Crawler};
use App\Jobs\{SavePagesJob, MultiCurlJob, OutputJob};
use Illuminate\View\View;

class CrawlerController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.index');
    }

    public function crawl(Request $request): View
    {
        $path = $request->input('path');

        $path = (empty($path)) ? '/' : $path;
        $path = ($path === '/') ? $path : ltrim($path, '/');
        $path = ($path === '/') ? $path : rtrim($path, '/');
        $path = ($path === '/') ? $path : '/' . $path;
        $response = Http::get(Crawler::ROOT_URL . $path);

        $validator = Validator::make($request->all(), [
            'path' => [
                function ($attribute, $value, $fail) use ($response) {
                    if ($response->getStatusCode() !== 200) {
                        $fail('The '.$attribute.' is invalid.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        $crawler = app()->makeWith(Crawler::class, ['response' => $response, 'path' => $path]);
        $crawler->savePage();
        $crawler->saveResources();
        $urls = Resource::getRemainingUrls($path, $crawler->getPage()->id, ($request->input('number_of_pages') - 1));
        $responses = MultiCurlJob::dispatchNow($urls);
        SavePagesJob::dispatchNow($responses, $urls, $crawler);

        return view('pages.crawler', OutputJob::dispatchNow($crawler->getCrawlerRequest()->id));
    }
}
