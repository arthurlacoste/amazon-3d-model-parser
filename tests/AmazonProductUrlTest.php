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
}
