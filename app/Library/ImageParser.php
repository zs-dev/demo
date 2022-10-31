<?php

declare(strict_types=1);

namespace App\Library;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use DOMDocument;
use DOMXPath;
use App\Models\{Page, CrawlerRequest, Resource};

class ImageParser implements ResourceParserInterface
{
    public function parse(DOMXPath $xPath): array
    {
        $tags = $xPath->query('//img[boolean(@data-src) or boolean(@src)]');
        $images = [];
        foreach ($tags as $tag) {
            $images[] = (!empty(trim($tag->getAttribute('src'))))
                ? $tag->getAttribute('src')
                : $tag->getAttribute('data-src');
        }

        return array_unique($images);
    }

    public function getResource(): string
    {
        return 'image';
    }
}