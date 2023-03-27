<?php

declare(strict_types=1);

namespace DMT\Slim\Controller\Attributes;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

#[Attribute(Attribute::TARGET_METHOD), NamedArgumentConstructor]
final class Route
{
    public function __construct(
        public readonly string $route,
        public readonly string|null $name = null,
        public readonly string|array $methods = 'GET',
        public readonly array $middleware = [],
    ) {
    }
}
