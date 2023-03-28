<?php

namespace DMT\Slim\Controller\Handlers\Strategies;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionFunction;
use Slim\Interfaces\InvocationStrategyInterface;

/**
 * Class FunctionParameters
 *
 * This strategy will check what arguments it needs and populates them.
 * It depends on PHP-8 named properties, like RequestResponseNamedArgs does,
 * but possible with some extra arguments, so it can act as any the other strategy.
 * These extra arguments are:
 *   - request (ServerRequest)
 *   - response (Response)
 *   - args (array)
 *   - queryParams (array)
 *   - parsedBody (mixed)
 *
 * NOTE: args defined take precedence over the extra arguments provided by this class.
 */
class FunctionParameters implements InvocationStrategyInterface
{
    public function __invoke(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $queryParams = $request->getQueryParams();

        $requestMethod = strtolower(string: $request->getMethod());
        $parsedBody = null;
        if (in_array(needle: $requestMethod, haystack: ['post', 'put', 'patch', 'delete'])) {
            $parsedBody = $request->getParsedBody();
        }

        $parameters = array_map(
            fn ($parameter) => $parameter->name,
            (new ReflectionFunction($callable(...)))->getParameters()
        );

        $arguments = $args + compact('request', 'response', 'args', 'queryParams', 'parsedBody');
        $arguments = array_intersect_key($arguments, array_flip($parameters));

        return $callable(...$arguments);
    }
}
