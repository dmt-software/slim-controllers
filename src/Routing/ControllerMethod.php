<?php

namespace DMT\Slim\Controller\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionMethod;
use Slim\Interfaces\InvocationStrategyInterface;

final class ControllerMethod implements InvocationStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ): ResponseInterface {
        $criteria = $request->getQueryParams();
        $requestMethod = strtolower(string: $request->getMethod());

        $resource = null;
        if (in_array(needle: $requestMethod, haystack: ['post', 'put', 'patch', 'delete'])) {
            $resource = $request->getParsedBody();
        }

        $parameters = array_map(
            fn ($parameter) => $parameter->name,
            (new ReflectionMethod(...$callable))->getParameters()
        );

        $arguments = array_filter(
            array: $routeArguments + compact('criteria', 'resource', 'request', 'response'),
            callback: fn($argument) => in_array($argument, $parameters),
            mode: ARRAY_FILTER_USE_KEY
        );

        return $callable(...$arguments);
    }
}
