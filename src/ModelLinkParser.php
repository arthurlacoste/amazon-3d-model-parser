<?php

declare(strict_types=1);

namespace Amazon3DModelParser;

final class ModelLinkParser
{
    public function parse(string $html, AmazonProductUrl $productUrl): ?ModelLink
    {
        $viewerUrl = $this->extractViewerUrl($html, $productUrl);
        if ($viewerUrl === null) {
            return null;
        }

        return new ModelLink($viewerUrl, $this->zipUrlFromViewerUrl($viewerUrl));
    }

    private function extractViewerUrl(string $html, AmazonProductUrl $productUrl): ?string
    {
        $domUrl = $this->extractFromDom($html, $productUrl);
        if ($domUrl !== null) {
            return $domUrl;
        }

        if (preg_match('@["\'](?P<url>https?://[^"\']+/view-3d[^"\']*)["\']@i', $html, $match)) {
            return html_entity_decode($match['url'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        if (preg_match('@["\'](?P<url>/view-3d[^"\']*)["\']@i', $html, $match)) {
            return $productUrl->baseUrl() . html_entity_decode($match['url'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return null;
    }

    private function extractFromDom(string $html, AmazonProductUrl $productUrl): ?string
    {
        $previous = libxml_use_internal_errors(true);
        $document = new \DOMDocument();
        $loaded = $document->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded) {
            return null;
        }

        foreach ($document->getElementsByTagName('a') as $link) {
            $href = $link->getAttribute('href');
            if (!str_contains($href, '/view-3d')) {
                continue;
            }

            return str_starts_with($href, 'http')
                ? $href
                : $productUrl->baseUrl() . html_entity_decode($href, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return null;
    }

    private function zipUrlFromViewerUrl(string $viewerUrl): ?string
    {
        $parts = parse_url($viewerUrl);
        if (!isset($parts['query'])) {
            return null;
        }

        parse_str($parts['query'], $query);
        if (empty($query['physicalId']) || !preg_match('/^[A-Za-z0-9_-]+$/', (string) $query['physicalId'])) {
            return null;
        }

        return 'https://m.media-amazon.com/images/I/' . rawurlencode((string) $query['physicalId']) . '.zip';
    }
}
