<?php

declare(strict_types=1);

namespace Amazon3DModelParser\Tests;

use Amazon3DModelParser\AmazonProductUrl;
use Amazon3DModelParser\ModelLinkParser;
use PHPUnit\Framework\TestCase;

final class ModelLinkParserTest extends TestCase
{
    public function testParsesRelativeViewerAndZipUrl(): void
    {
        $productUrl = AmazonProductUrl::fromString('https://www.amazon.fr/dp/B0CK3FLWYZ');
        self::assertNotNull($productUrl);

        $html = '<a href="/view-3d?physicalId=ABC_123-def&amp;ref=dp">View in 3D</a>';
        $model = (new ModelLinkParser())->parse($html, $productUrl);

        self::assertNotNull($model);
        self::assertSame('https://www.amazon.fr/view-3d?physicalId=ABC_123-def&ref=dp', $model->viewerUrl);
        self::assertSame('https://m.media-amazon.com/images/I/ABC_123-def.zip', $model->zipUrl);
    }

    public function testParsesAbsoluteViewerUrl(): void
    {
        $productUrl = AmazonProductUrl::fromString('https://www.amazon.fr/dp/B0CK3FLWYZ');
        self::assertNotNull($productUrl);

        $html = '"https://www.amazon.fr/view-3d?physicalId=XYZ987"';
        $model = (new ModelLinkParser())->parse($html, $productUrl);

        self::assertNotNull($model);
        self::assertSame('https://www.amazon.fr/view-3d?physicalId=XYZ987', $model->viewerUrl);
    }

    public function testReturnsNullWithoutViewerLink(): void
    {
        $productUrl = AmazonProductUrl::fromString('https://www.amazon.fr/dp/B0CK3FLWYZ');
        self::assertNotNull($productUrl);

        self::assertNull((new ModelLinkParser())->parse('<html></html>', $productUrl));
    }
}
