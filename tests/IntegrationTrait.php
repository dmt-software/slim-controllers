<?php

namespace DMT\Test\Slim\Controller;

use DMT\DependencyInjection\Container;
use DMT\DependencyInjection\ContainerFactory;
use DMT\Slim\Controller\ControllerServiceProvider;
use Pimple\Container as PimpleContainer;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;

/**
 * @group integration
 * @method expectOutputRegex(string $string)
 */
trait IntegrationTrait
{
    public function getApp(): App
    {
        $container = (new ContainerFactory())->createContainer(new PimpleContainer());
        $application = AppFactory::create(container: $container);

        $container->set(id: App::class, value: fn() => $application);
        $container->set(id: ResponseFactoryInterface::class, value: fn() => $application->getResponseFactory());
        $container->register(provider: new ControllerServiceProvider(__DIR__ . '/../tests'));

        $this->expectOutputRegex('~.*~');

        return $application;
    }
}
