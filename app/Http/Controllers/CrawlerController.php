<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\{Client, Pool};
use GuzzleHttp\Psr7\Request as Req;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use App\Models\{Page, CrawlerRequest, Resource};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Client\Pool as LaravePool;
use Illuminate\Support\Facades\Http as LaravelHttp;
use App\Library\Crawler;
use App\Library\CrawlerDbActions;
use App\Jobs\MultiCurlJob;
use App\Jobs\CreateCrawlerJob;
use App\Jobs\OutputJob;

class CrawlerController extends Controller {

    public function index(Request $request)
    {
        return view('pages.index', ['name' => 'James']);
    }

    public function crawl(Request $request)
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
        CreateCrawlerJob::dispatchNow($responses, $urls, $crawler);

        return view('pages.crawler', OutputJob::dispatchNow($crawler->getCrawlerRequest()->id));

dd($responses);

dd($crawler);
    $doc = new \DOMDocument();
    $internalErrors = libxml_use_internal_errors(true);
    $doc->loadHTML($response->getBody()->getContents()); // the variable $ads contains the HTML code above
    libxml_use_internal_errors($internalErrors);

    $xPath = new \DOMXPath($doc);

//dd($xPath->query('//title')->item(0)->textContent);
$crawlerRequest = CrawlerRequest::create([
    // 'word_count' => str_word_count($xPath->query('//html')->item(0)->textContent),
    // 'load_time' => $response->transferStats->getTransferTime()
]);

$page = Page::create([
    'path' => $path,
    'status' => $response->getStatusCode(),
    'title' => (trim($xPath->query('//title')->item(0)->textContent)) ? trim($xPath->query('//title')->item(0)->textContent) : null,
    'crawler_request_id' => $crawlerRequest->id,
    'word_count' => str_word_count($xPath->query('//html')->item(0)->textContent),
    // 'word_count' => str_word_count($xPath->query('//html')->item(0)->textContent),
    // 'load_time' => $response->transferStats->getTransferTime()
]);

// DB::table('resources')->insertOrIgnore([
//     ['id' => 1, 'email' => 'sisko@example.com'],
//     ['id' => 2, 'email' => 'archer@example.com'],
// ]);


    //$xPath = new \DOMXPath($doc);
    $tags = $xPath->query('//img[boolean(@data-src) or boolean(@src)]');
    $images = [];
    foreach ($tags as $tag) {
        $images[] = (!empty(trim($tag->getAttribute('src')))) ? $tag->getAttribute('src') : $tag->getAttribute('data-src');
    }
    $images = array_unique($images);
    $images = collect($images);
    foreach ($images->chunk(500)->toArray() as $chunk) {
        $records = [];
        foreach ($chunk as $image) {
            $records[] = ['page_id' => $page->id, 'path' => $image, 'resource' => 'image'];
        }
        DB::table('resources')->insertOrIgnore($records);
    }


    $internal = [];
    $external = [];

    $tags = $xPath->query('//a');
    foreach ($tags as $tag) {
        // dump($tag->getAttribute('href'));

        // dd($tag->getAttribute('href'));
        if (preg_match('/(http|https)/', $tag->getAttribute('href'))) {
            $external[] = $tag->getAttribute('href');

        } else {
            $internal[] = $tag->getAttribute('href');
        }
     }
    $internal = array_unique($internal);
    $external = array_unique($external);

    $internal = collect($internal);
    $external = collect($external);
    foreach ($internal->chunk(500)->toArray() as $chunk) {
        $records = [];
        foreach ($chunk as $internalPath) {
            $records[] = ['page_id' => $page->id, 'path' => $internalPath, 'resource' => 'internal_link'];
        }
        DB::table('resources')->insertOrIgnore($records);
    }

    foreach ($external->chunk(500)->toArray() as $chunk) {
        $records = [];
        foreach ($chunk as $externalPath) {
            $records[] = ['page_id' => $page->id, 'path' => $externalPath, 'resource' => 'external_link'];
        }
        DB::table('resources')->insertOrIgnore($records);
    }

    $urls = Resource::whereNotIn('path', [$path])
    ->where('resource', 'internal_link')
    ->where('page_id', $page->id)
    ->inRandomOrder()
    ->limit($request->input('number_of_pages'))
    ->get(['path']);

    $urls = $urls->toArray();
    $responses = Http::pool(function(LaravePool $pool) use ($urls) {
       foreach ($urls as $url) {
           $pool->get('https://agencyanalytics.com/' . ltrim($url['path'], '/'));
       }
    });


dd($responses);


// $urls = [];
// $urls[] = 'https://agencyanalytics.com/';
// $urls[] = 'https://agencyanalytics.com/feature/automated-marketing-reports';

// $responses = Http::pool(fn (LaravePool $pool) => [
//     $pool->get($urls[0]),
//     $pool->get($urls[1]),
// ]);
// dump($responses[0]);
// dd($responses[1]);

dd($urls);


    // print_r(count($images));
    // echo "<br>";
    // print_r(count(array_unique($images)));

    die;

// get time
dd($response->transferStats->getTransferTime());
die;


$start = microtime(true);
$client = new Client();
$requests = [];
$requests[] = new Req('GET', 'https://agencyanalytics.com/');

// $requests[] = new Req('GET', 'https://httpbin.org');
// $requests[] = new Req('GET', 'http://youtube.com');
// $requests[] = new Req('GET', 'http://yahoo.com');
// $requests[] = new Req('GET', 'http://google.com');
// $requests[] = new Req('GET', 'http://cnn.com');
$pool_batch = Pool::batch($client, $requests);
dd($pool_batch);
foreach ($pool_batch as $pool => $res) {

    if ($res instanceof RequestException) {
        // Do sth
        continue;
    }
//     echo '<pre>';
//     echo htmlspecialchars($res->getBody()->getContents());
//     echo '</pre>';
// die;

// $content = preg_replace("/&#?[a-z0-9]{2,8};/i","",$res->getBody()->getContents());
// print_r($res->getBody()->__toString());
// die;

    $title = '[No Title Tag Found]';
    $d = new \DOMDocument();
    $internalErrors = libxml_use_internal_errors(true);
    $d->loadHTML($res->getBody()->getContents()); // the variable $ads contains the HTML code above
    libxml_use_internal_errors($internalErrors);

    $script = $d->getElementsByTagName('script')->item(0);
    $script->parentNode->removeChild($script);
    $body = $d->getElementsByTagName('body')->item(0);
    echo $body->nodeValue;
    die;

    $xpath = new \DOMXPath($d);
    $node = $xpath->query('//html')->item(0);
    echo $node->textContent; // text
die;
    $xp = new \DOMXPath($d);
//*[boolean(@foo)]
    $nodes = $xp->query('//img[boolean(@data-src)]');
    // $nodes = array_unique(iterator_to_array($nodes));
    // print_r( $nodes);die;
    $a = [];
    $b = [];
    foreach ($nodes as $tag) {
        $a[] = $tag->getAttribute('data-src');
        $b[] = $tag->getAttribute('data-src');
    }

    // $text = strip_tags($this->orginal_content);
    // $text = str_replace('&nbsp;',"",$text);
    // $this->orginal_content_count = str_word_count($text);

    print_r(count($a));
    echo "<br>";
    print_r(count(array_unique($a)));
die;
    print_r(get_class_methods(get_class($nodes)));
print_r($nodes);
die;
    $tags = $d->getElementsByTagName('img');

    foreach ($tags as $tag) {
          // echo $tag->getAttribute('data-src') . "<br>";
    }
    foreach ($tags as $tag) {
        echo $tag->getAttribute('src') . "<br>";
    }
    die;
    $xpath = new \DOMXPath($d);




    $ls_ads = $xpath->query('//link');
   // print_r($ls_ads->length);
    for ($i = 0; $i < $ls_ads->length; $i++) {
        //print_r( get_class_methods(get_class($ls_ads->item($i))));die;
        $books = $ls_ads->item($i)->getElementsByTagName('href');
        print_r( get_class($ls_ads->item($i)));die;
        foreach ($books as $book) {
            echo $book->nodeValue, PHP_EOL;
        }
    }
    // foreach ($ls_ads->item() as $node) {
    //     echo $node->nodeValue ;
    //   }
     print_r(get_class_methods(get_class($ls_ads) ));
    die;
    if (preg_match("#<title>(.*)</title>#i", $res->getBody(), $out)) {
        $title = $out[1];
    }

    echo "Fetch complete for (" . $res->getStatusCode() . ") $title <br>" . PHP_EOL;
}
echo microtime(true) - $start;
    }
}
