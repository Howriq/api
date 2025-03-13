<?php

declare(strict_types=1);

namespace Api\App\Route;

class Route extends AbstractRoute
{
    public function __construct(
        ?string $path = null,
        array|string|null $middlewares = null,
        ?string $name = null,
        ?string $method = null
    ) {
        parent::__construct($path, $middlewares, $name, $method);
    }

    public function delete(string $path, string $handler, string $name): RouteInterface
    {
        return new Delete($path, $handler, $name);
    }

    public function get(string $path, string $handler, string $name): RouteInterface
    {
        return new Get($path, $handler, $name);
    }

    public function patch(string $path, string $handler, string $name): RouteInterface
    {
        return new Patch($path, $handler, $name);
    }

    public function post(string $path, string $handler, string $name): RouteInterface
    {
        return new Post($path, $handler, $name);
    }

    public function put(string $path, string $handler, string $name): RouteInterface
    {
        return new Put($path, $handler, $name);
    }

    public function redirect(string $path, string $handler, string $name): self
    {
        return $this;
    }
}
