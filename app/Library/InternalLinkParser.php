<?php

declare(strict_types=1);

namespace App\Library;

use DOMXPath;

class InternalLinkParser implements ResourceParserInterface
{
    /**
     * Parse internal links.
     *
     * @param DOMXPath $xPath
     *
     * @return array
     */
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

    /**
     * Return resource type.
     *
     * @return string
     */
    public function getResource(): string
    {
        return 'internal_link';
    }
}
