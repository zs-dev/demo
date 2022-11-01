<?php

declare(strict_types=1);

namespace App\Library;

use DOMXPath;

class ImageParser implements ResourceParserInterface
{
    /**
     * Parse image sources.
     *
     * @param DOMXPath $xPath
     *
     * @return array
     */
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

    /**
     * Return resource type.
     *
     * @return string
     */
    public function getResource(): string
    {
        return 'image';
    }
}
