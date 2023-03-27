<?php

namespace DMT\Slim\Controller\Routing;

use DMT\Slim\Controller\Attributes\Route;
use DMT\Slim\Controller\Attributes\RouteGroup;
use Kcs\ClassFinder\Finder\FinderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;
use ReflectionMethod;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Spiral\Attributes\ReaderInterface;

class ControllerRouteCollector
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly RouteCollectorProxyInterface $routeCollector,
        private ?ReaderInterface $reader = null,
        private ?FinderInterface $finder = null
    ) {
        $this->reader ??= $this->container->get(ReaderInterface::class);
        $this->finder ??= $this->container->get(FinderInterface::class);
    }

    public function register(string $path): void
    {
        foreach ($this->finder->in($path)->subclassOf(Controller::class) as $reflector) {
            if ($routeGroup = $this->reader->firstClassMetadata($reflector, RouteGroup::class)) {
                $this->registerRouteGroup($routeGroup, $reflector, $this->routeCollector);
                continue;
            }

            foreach ($reflector->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if ($route = $this->reader->firstFunctionMetadata($method, Route::class)) {
                    $this->registerRoute($route, $method, $this->routeCollector);
                }
            }
        }
    }

    private function registerRouteGroup(
        RouteGroup $routeGroup,
        ReflectionClass $class,
        RouteCollectorProxyInterface $routeCollector
    ): void {
        $collector = $this;
        $group = $routeCollector->group(
            pattern: $routeGroup->route,
            callable: function (RouteCollectorProxyInterface $routeCollector) use ($collector, $class) {
                foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                    if ($route = $collector->reader->firstFunctionMetadata($method, Route::class)) {
                        $collector->registerRoute($route, $method, $routeCollector);
                    }
                }
            }
        );
        if (count($routeGroup->middleware)) {
            array_map([$group, 'add'], $routeGroup->middleware);
        }
    }

    private function registerRoute(
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

        $route->setInvocationStrategy(new ControllerMethod());
    }
}
