<?php

declare(strict_types=1);

namespace App\Library;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use DOMDocument;
use DOMXPath;
use App\Models\{Page, CrawlerRequest, Resource};

class ExternalLinkParser implements ResourceParserInterface
{
    public function parse(DOMXPath $xPath): array
    {
        $external = [];

        $tags = $xPath->query('//a');
        foreach ($tags as $tag) {
            if (preg_match('/(http|https)/', $tag->getAttribute('href'))) {
                $external[] = $tag->getAttribute('href');
            }
         }

        return $external;
    }

    public function getResource(): string
    {
        return 'external_link';
    }
}