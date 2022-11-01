<?php

declare(strict_types=1);

namespace App\Library;

use DOMXPath;

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
