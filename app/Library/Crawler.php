<?php

declare(strict_types=1);

namespace App\Library;

use Illuminate\Http\Client\Response;
use DOMDocument;
use DOMXPath;
use App\Models\{Page, CrawlerRequest};

class Crawler
{
    public const ROOT_URL = 'https://agencyanalytics.com';

    private DOMXPath $xPath;
    private ResourceParserInterface $resourceParser;
    private Page $page;

    /**
     * Crawler constructor.
     *
     * @param Response $response
     * @param string $path
     * @param CrawlerRequest $crawlerRequest
     * @param CrawlerDbActions $crawlerDbActions
     */
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

    /**
     * Save Page.
     *
     * @return Page
     */
    public function savePage(): Page
    {
        $this->page = $this->crawlerDbActions->savePage($this->path, $this->response, $this->xPath, $this->crawlerRequest);

        return $this->page;
    }

    /**
     * Save Page's Resources.
     *
     * @return void
     */
    public function saveResources()
    {
        $parsers = [new ImageParser(), new InternalLinkParser(), new ExternalLinkParser()];
        foreach ($parsers as $parser) {
            $data = $parser->parse($this->xPath);
            $this->crawlerDbActions->saveResource($data, $this->page, $parser->getResource());
        }
    }

    /**
     * Get $page property.
     *
     * @return Page
     */
    public function getPage(): Page
    {
        return $this->page;
    }

    /**
     * Get $crawlerRequest property.
     *
     * @return CrawlerRequest
     */
    public function getCrawlerRequest(): CrawlerRequest
    {
        return $this->crawlerRequest;
    }

    /**
     * Set $resourceParser property.
     *
     * @param ResourceParserInterface $resourceParser
     */
    public function setParser(ResourceParserInterface $resourceParser)
    {
        $this->resourceParser = $resourceParser;
    }

    /**
     * Parse xpath for resource.
     *
     * @return array
     */
    public function parse(): array
    {
        return $this->resourceParser->parse($this->xPath);
    }

    /**
     * Set $path property.
     *
     * @param string $path
     *
     * @return Crawler
     */
    public function setPath(string $path): Crawler
    {
        if ($path === '/') {
            $this->path = $path;
        } else {
            $this->path = rtrim($path, '/');
        }

        return $this;
    }

    /**
     * Set $xPath property.
     *
     * @param Response $response
     *
     * @return Crawler
     */
    public function setXPath(Response $response): Crawler
    {
        $doc = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);
        $doc->loadHTML($this->response->getBody()->getContents());
        libxml_use_internal_errors($internalErrors);
        $this->xPath = new DOMXPath($doc);

        return $this;
    }

    /**
     * Set $response property.
     *
     * @param Response $response
     *
     * @return Crawler
     */
    public function setResponse(Response $response): Crawler
    {
        $this->response = $response;

        return $this;
    }
}
