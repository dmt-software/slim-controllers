<?php

namespace DMT\Slim\Controller\ServiceProviders;

use DMT\DependencyInjection\Container;
use DMT\DependencyInjection\ServiceProviderInterface;
use DMT\Slim\Controller\Routing\ControllerMapper;
use Kcs\ClassFinder\Finder\ComposerFinder;
use Kcs\ClassFinder\Finder\FinderInterface;
use Slim\App;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\ReaderInterface;

class ControllerMapperProvider implements ServiceProviderInterface
{
    public function __construct(private readonly App $app)
    {
    }

    public function register(Container $container): void
    {
        if (!$container->has(id: ReaderInterface::class)) {
            $container->set(id: ReaderInterface::class, value: fn() => new AttributeReader());
        }

        $container->set(
            id: ControllerMapper::class,
            value: fn() => new ControllerMapper($this->app, $container->get(ReaderInterface::class))
        );
    }
}
