<?php

declare(strict_types=1);

namespace App\Library;

use DOMXPath;

class InternalLinkParser implements ResourceParserInterface
{
    public function parse(DOMXPath $xPath): array
    {
        $internal = [];
        $tags = $xPath->query('//a');
        foreach ($tags as $tag) {
            if (!preg_match('/(http|https)/', $tag->getAttribute('href'))) {
                $internal[] = $tag->getAttribute('href');
            }
        }

        return $internal;
    }

    public function getResource(): string
    {
        return 'internal_link';
    }
}
