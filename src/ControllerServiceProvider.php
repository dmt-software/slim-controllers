<?php

namespace DMT\Slim\Controller;

use DMT\DependencyInjection\Container;
use DMT\DependencyInjection\ServiceProviderInterface;
use DMT\Slim\Controller\Routing\ControllerRouteCollector;
use Kcs\ClassFinder\Finder\ComposerFinder;
use Kcs\ClassFinder\Finder\FinderInterface;
use Slim\App;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\ReaderInterface;

class ControllerServiceProvider implements ServiceProviderInterface
{
    public function __construct(public readonly string $path = 'src/Controllers')
    {
    }

    public function register(Container $container): void
    {
        if (!$container->has(id: FinderInterface::class)) {
            $container->set(id: FinderInterface::class, value: fn() => new ComposerFinder());
        }
        if (!$container->has(id: ReaderInterface::class)) {
            $container->set(id: ReaderInterface::class, value: fn() => new AttributeReader());
        }

        $container->set(
            id: ControllerRouteCollector::class,
            value: fn() => new ControllerRouteCollector($container, $container->get(id: App::class))
        );

        /** Register all controllers in path as routes */
        $collector = $container->get(ControllerRouteCollector::class);
        $collector->register(path: $this->path);
    }
}
