<?php

declare(strict_types=1);

namespace Amazon3DModelParser;

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
