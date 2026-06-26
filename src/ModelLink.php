<?php

declare(strict_types=1);

namespace Amazon3DModelParser;

final readonly class ModelLink
{
    public function __construct(
        public string $viewerUrl,
        public ?string $zipUrl,
    ) {
    }
}
