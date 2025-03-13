<?php

declare(strict_types=1);

namespace Api\App\Route;

use Mezzio\Application;

use function array_search;
use function in_array;
use function is_string;
use function sprintf;

class RouteGroup
{
    /** @var RouteInterface[] $routes */
    private array $routes;
    private string $prefix;
    private array $middlewares = [];

    public function __construct(
        ?string $prefix = null,
        array|string|null $middlewares = null,
    ) {
        $prefix && $this->setPrefix($prefix);
        $middlewares && $this->setMiddlewares($middlewares);
    }

    public function addRoute(RouteInterface $route): self
    {
        $this->routes[] = $route;

        return $this;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @param class-string $middleware
     */
    public function addMiddleware(string $middleware): self
    {
        if (! in_array($middleware, $this->middlewares, true)) {
            $this->middlewares[] = $middleware;
        }

        return $this;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @param class-string[] $middlewares
     */
    public function setMiddlewares(array|string $middlewares): self
    {
        if (is_string($middlewares)) {
            $middlewares = [$middlewares];
        }

        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }

        return $this;
    }

    public function register(Application $app): void
    {
        foreach ($this->routes as $route) {
            $route
                ->setPath(sprintf('%s%s', $this->prefix, $route->getPath()))
                ->setMiddlewares(
                    $this->combineMiddlewares(
                        $this->middlewares,
                        $route->getMiddlewares(),
                        $route->getExcludedMiddlewares()
                    )
                )
                ->register($app);
        }
    }

    private function combineMiddlewares(
        array $routeGroupMiddlewares = [],
        array $routeMiddlewares = [],
        array $excludedMiddlewares = []
    ): array {
        $middlewares = $routeGroupMiddlewares;

        foreach ($routeMiddlewares as $routeMiddleware) {
            if (in_array($routeMiddleware, $middlewares, true)) {
                continue;
            }
            $middlewares[$routeMiddleware] = $routeMiddleware;
        }

        foreach ($excludedMiddlewares as $excludedMiddleware) {
            if (in_array($excludedMiddleware, $middlewares)) {
                unset($middlewares[array_search($excludedMiddleware, $middlewares, true)]);
            }
        }

        return $middlewares;
    }
}
