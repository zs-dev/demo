<?php

declare(strict_types=1);

namespace App\Library;

use DOMXPath;

interface ResourceParserInterface
{
    public function parse(DOMXPath $xPath): array;
    public function getResource(): string;
}
