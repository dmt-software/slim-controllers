<?php

namespace DMT\Slim\Controller\Attributes;

use Attribute;
use Spiral\Attributes\NamedArgumentConstructor;

#[Attribute(Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class RouteGroup
{
    public function __construct(
        public readonly string $route,
        public readonly array $middleware = [],
    ) {
    }
}
