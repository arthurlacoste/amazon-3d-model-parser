<?php

declare(strict_types=1);

namespace Amazon3DModelParser\Tests;

use Amazon3DModelParser\AmazonProductUrl;
use PHPUnit\Framework\TestCase;

final class AmazonProductUrlTest extends TestCase
{
    public function testAcceptsAmazonProductUrl(): void
    {
        $url = AmazonProductUrl::fromString('https://www.amazon.fr/dp/B0CK3FLWYZ');

        self::assertNotNull($url);
        self::assertSame('https://www.amazon.fr', $url->baseUrl());
        self::assertSame('.amazon.fr', $url->cookieDomain());
    }

    public function testRejectsNonAmazonUrl(): void
    {
        self::assertNull(AmazonProductUrl::fromString('https://example.com/dp/B0CK3FLWYZ'));
    }

    public function testRejectsUnsupportedScheme(): void
    {
        self::assertNull(AmazonProductUrl::fromString('ftp://www.amazon.fr/dp/B0CK3FLWYZ'));
    }

    public function testAcceptsAmazonShortUrl(): void
    {
        $url = AmazonProductUrl::fromString('https://amzn.to/4eMEcU2');

        self::assertNotNull($url);
        self::assertTrue($url->isShortUrl());
    }

    public function testValidatesAmazonDestinationWithoutShortHosts(): void
    {
        self::assertTrue(AmazonProductUrl::isAmazonDestination('https://www.amazon.fr/dp/B0CK3FLWYZ'));
        self::assertFalse(AmazonProductUrl::isAmazonDestination('https://amzn.to/4eMEcU2'));
        self::assertFalse(AmazonProductUrl::isAmazonDestination('https://example.com'));
    }
}
