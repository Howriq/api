<?php

declare(strict_types=1);

namespace Api\App\Route;

use Mezzio\Application;

interface RouteInterface
{
    /**
     * @param class-string $middleware
     */
    public function addMiddleware(string $middleware): self;

    public function getMiddlewares(): array;

    /**
     * @param class-string[]|class-string $middlewares
     */
    public function setMiddlewares(array|string $middlewares): self;

    public function getName(): string;

    /**
     * @param non-empty-string $name
     */
    public function setName(string $name): self;

    public function getPath(): string;

    /**
     * @param non-empty-string $path
     */
    public function setPath(string $path): self;

    public function getMethod(): string;

    /**
     * @param non-empty-string $method
     */
    public function setMethod(string $method): self;

    public function register(Application $app): void;

    /**
     * @param class-string[]|class-string $middlewares
     */
    public function excludeMiddlewares(array|string $middlewares): self;

    public function getExcludedMiddlewares(): array;
}
