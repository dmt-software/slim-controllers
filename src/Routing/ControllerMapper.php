<?php

namespace DMT\Slim\Controller\Routing;

use DMT\Slim\Controller\Attributes\Route;
use DMT\Slim\Controller\Attributes\RouteGroup;
use DMT\Slim\Controller\Handlers\Strategies\FunctionParameters;
use DMT\Slim\Controller\Interfaces\ResourceControllerInterface;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;
use Slim\App;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Spiral\Attributes\ReaderInterface;

class ControllerMapper
{
    public function __construct(
        private readonly App|RouteCollectorProxyInterface $routeCollector,
        private readonly ReaderInterface $reader,
        private readonly InvocationStrategyInterface $handlerStrategy = new FunctionParameters()
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function mapController(string $controller, RouteCollectorProxyInterface $routeCollector = null): void
    {
        $routeCollector ??= $this->routeCollector;
        $routeCollector->getRouteCollector()->setDefaultInvocationStrategy($this->handlerStrategy);

        if (!class_exists($controller)) {
            throw new InvalidArgumentException('controller not found');
        }

        $reflector = new ReflectionClass($controller);
        $routeGroup = $this->reader->firstClassMetadata(class: $reflector, name: RouteGroup::class);

        if ($reflector->implementsInterface(ResourceControllerInterface::class) && !$routeGroup) {
            throw new InvalidArgumentException('missing route-group for resource controller');
        }

        if ($routeGroup) {
            $this->group($routeGroup, $reflector, $routeCollector);
            return;
        }

        foreach ($reflector->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($route = $this->reader->firstFunctionMetadata($method, Route::class)) {
                $this->map($route, $method, $routeCollector);
            }
        }
    }

    private function group(
        RouteGroup $routeGroup,
        ReflectionClass $class,
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $mapper = $this;
        $group = $routeCollector->group(
            pattern: $routeGroup->route,
            callable: function (RouteCollectorProxyInterface $routeCollector) use ($mapper, $class) {
                foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                    if ($route = $mapper->reader->firstFunctionMetadata($method, Route::class)) {
                        $mapper->map($route, $method, $routeCollector);
                    }
                }
            }
        );
        if (count($routeGroup->middleware)) {
            array_map([$group, 'add'], $routeGroup->middleware);
        }
    }

    private function map(
        Route $routing,
        ReflectionMethod $method,
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $route = $routeCollector->map(
            methods: (array)$routing->methods,
            pattern: $routing->route,
            callable: [$method->class, $method->name]
        );

        if (count($routing->middleware)) {
            array_map([$route, 'add'], $routing->middleware);
        }
    }
}
