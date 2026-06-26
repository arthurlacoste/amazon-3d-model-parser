<?php

declare(strict_types=1);

namespace Amazon3DModelParser;

final readonly class FetchResult
{
    private function __construct(
        public bool $ok,
        public ?string $body,
        public ?string $error,
    ) {
    }

    public static function success(string $body): self
    {
        return new self(true, $body, null);
    }

    public static function failure(string $error): self
    {
        return new self(false, null, $error);
    }
}
