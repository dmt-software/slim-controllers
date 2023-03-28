<?php

namespace DMT\Test\Slim\Controller;

use DMT\DependencyInjection\ContainerFactory;
use DMT\Slim\Controller\Handlers\Strategies\FunctionParameters;
use DMT\Slim\Controller\ServiceProviders\ControllerMapperProvider;
use Pimple\Container as PimpleContainer;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Spiral\Attributes\Reader;

/**
 * @group integration
 * @method expectOutputRegex(string $string)
 */
trait IntegrationTrait
{
    public function getApp(): App
    {
        $container = (new ContainerFactory())->createContainer(new PimpleContainer());
        $app = AppFactory::create(container: $container);

        $container->set(id: App::class, value: fn() => $app);
        $container->set(id: ResponseFactoryInterface::class, value: fn() => $app->getResponseFactory());
        $container->register(provider: new ControllerMapperProvider($app));

        $this->expectOutputRegex('~.*~');

        $routeCollector = $app->getRouteCollector();
        $routeCollector->setDefaultInvocationStrategy(new FunctionParameters());

        return $app;
    }
}
