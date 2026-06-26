<?php

declare(strict_types=1);

namespace Amazon3DModelParser;

final readonly class FetchResult
{
    private function __construct(
        public bool $ok,
        public ?string $body,
        public ?string $effectiveUrl,
        public ?string $error,
    ) {
    }

    public static function success(string $body, string $effectiveUrl): self
    {
        return new self(true, $body, $effectiveUrl, null);
    }

    public static function failure(string $error): self
    {
        return new self(false, null, null, $error);
    }
}
