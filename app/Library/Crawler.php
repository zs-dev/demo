<?php

declare(strict_types=1);

namespace App\Library;

use Illuminate\Http\Client\Response;
use DOMDocument;
use DOMXPath;
use App\Models\{Page, CrawlerRequest};
use App\Library\CrawlerDbActions;

class Crawler
{
    public const ROOT_URL = 'https://agencyanalytics.com';

    //private Response $response;
    private DOMXPath $xPath;
    private ResourceParserInterface $resourceParser;
    private Page $page;

    public function __construct(
        private Response $response,
        private string $path,
        private CrawlerRequest $crawlerRequest,
        private CrawlerDbActions $crawlerDbActions,
    ) {
        $this->setPath($this->path);
        $this->setXPath($this->response);
        $this->crawlerDbActions = app()->make(CrawlerDbActions::class);
    }

    public function savePage(): Page
    {
        $this->page = $this->crawlerDbActions->savePage($this->path, $this->response, $this->xPath, $this->crawlerRequest);

        return $this->page;
    }

    public function saveResources(): void
    {
        $parsers = [new ImageParser(), new InternalLinkParser(), new ExternalLinkParser()];
        foreach ($parsers as $parser) {
            $data = $parser->parse($this->xPath);
            $this->crawlerDbActions->saveResource($data, $this->page, $parser->getResource());
        }
    }

    public function getPage(): Page
    {
        return $this->page;
    }

    public function getCrawlerRequest(): CrawlerRequest
    {
        return $this->crawlerRequest;
    }

    public function setParser(ResourceParserInterface $resourceParser)
    {
        $this->resourceParser = $resourceParser;
    }

    public function parse(): array
    {
        return $this->resourceParser->parse($this->xPath);
    }

    public function setPath(string $path): Crawler
    {
        if ($path === '/') {
            $this->path = $path;
        } else {
            $this->path = rtrim($path, '/');
        }

        return $this;
    }

    public function setXPath(Response $response): Crawler
    {
        $doc = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);
        $doc->loadHTML($this->response->getBody()->getContents());
        libxml_use_internal_errors($internalErrors);
        $this->xPath = new DOMXPath($doc);

        return $this;
    }

    public function setResponse(Response $response): Crawler
    {
        $this->response = $response;

        return $this;
    }
}
