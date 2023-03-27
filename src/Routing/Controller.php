<?php

namespace DMT\Slim\Controller\Routing;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

abstract class Controller
{
    protected ?ResponseInterface $response;

    public function __construct(protected readonly ContainerInterface $container)
    {
        $this->response = $container->get(id: ResponseFactoryInterface::class)->createResponse();
    }
}
