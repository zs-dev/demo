<?php

declare(strict_types=1);

namespace App\Library;

use DOMXPath;

class ExternalLinkParser implements ResourceParserInterface
{
    /**
     * Parse external links.
     *
     * @param DOMXPath $xPath
     *
     * @return array
     */
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

    /**
     * Return resource type.
     *
     * @return string
     */
    public function getResource(): string
    {
        return 'external_link';
    }
}
