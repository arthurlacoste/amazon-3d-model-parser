<?php

declare(strict_types=1);

namespace Amazon3DModelParser;

final readonly class AmazonProductUrl
{
    private const SHORT_HOSTS = ['amzn.to'];

    public function __construct(
        public string $original,
        public string $scheme,
        public string $host,
    ) {
    }

    public static function fromString(string $url): ?self
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        $parts = parse_url($url);
        if (!is_array($parts)) {
            return null;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        $host = self::normalizeHost((string) ($parts['host'] ?? ''), true);
        if ($host === null) {
            return null;
        }

        return new self($url, $scheme, $host);
    }

    public function baseUrl(): string
    {
        return $this->scheme . '://' . $this->host;
    }

    public function cookieDomain(): string
    {
        return '.' . preg_replace('/^www\./', '', $this->host);
    }

    public function isShortUrl(): bool
    {
        return in_array($this->host, self::SHORT_HOSTS, true);
    }

    public static function isAmazonDestination(string $url): bool
    {
        $parts = parse_url($url);
        if (!is_array($parts)) {
            return false;
        }

        return self::normalizeHost((string) ($parts['host'] ?? ''), false) !== null;
    }

    private static function normalizeHost(string $host, bool $allowShortUrls): ?string
    {
        $host = strtolower(trim($host, ". \t\n\r\0\x0B"));
        if ($host === '') {
            return null;
        }

        if ($allowShortUrls && in_array($host, self::SHORT_HOSTS, true)) {
            return $host;
        }

        $labels = explode('.', $host);
        if (count($labels) < 2) {
            return null;
        }

        $amazonIndex = array_search('amazon', $labels, true);
        if ($amazonIndex === false || $amazonIndex === count($labels) - 1) {
            return null;
        }

        foreach (array_slice($labels, $amazonIndex + 1) as $label) {
            if (!preg_match('/^[a-z]{2,}$/', $label)) {
                return null;
            }
        }

        return $host;
    }
}
