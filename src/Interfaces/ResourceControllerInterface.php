<?php

namespace DMT\Slim\Controller\Interfaces;

use DMT\Slim\Controller\Attributes\Route;
use DMT\Slim\Controller\Attributes\RouteGroup;
use Psr\Http\Message\ResponseInterface;

#[RouteGroup(route: "/{% resource-route %}")]
interface ResourceControllerInterface
{
    #[Route(route: "", methods: "GET")]
    public function index(array $queryParams = null): ResponseInterface;

    #[Route(route: "/create", methods: "GET")]
    public function create(array $parsedBody = null): ResponseInterface;

    #[Route(route: "", methods: "POST")]
    public function store(array $parsedBody = null): ResponseInterface;

    #[Route(route: "/{id: [0-9]+}", methods: "GET")]
    public function show(int $id): ResponseInterface;

    #[Route(route: "/{id: [0-9]+}/edit", methods: "GET")]
    public function edit(int $id): ResponseInterface;

    #[Route(route: "/{id: [0-9]+}", methods: "PUT")]
    public function update(int $id, array $parsedBody = null): ResponseInterface;

    #[Route(route: "/{id: [0-9]+}", methods: "DELETE")]
    public function destroy(int $id): ResponseInterface;
}
