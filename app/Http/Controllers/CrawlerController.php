<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Resource;
use Illuminate\Support\Facades\Validator;
use App\Library\Crawler;
use App\Jobs\{SavePagesJob, MultiCurlJob, OutputJob};
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CrawlerController extends Controller
{
    /**
     * Displays initial form.
     *
     * @param Request $request
     *
     * @return View
     */
    public function index(Request $request): View
    {
        return view('pages.index');
    }

    /**
     * Process form data for crawling.
     *
     * @param Request $request
     *
     * @return View|RedirectResponse
     */
    public function crawl(Request $request): View|RedirectResponse
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
