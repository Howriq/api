<?php

declare(strict_types=1);

namespace Api\App\Route;

use Fig\Http\Message\RequestMethodInterface;
use Mezzio\Application;

use function in_array;
use function is_string;

abstract class AbstractRoute implements RouteInterface
{
    /** @var class-string[] $excludedMiddlewares */
    protected array $excludedMiddlewares = [];
    /** @var class-string[] $middlewares */
    protected array $middlewares = [];
    /** @var non-empty-string $name */
    protected string $name;
    /** @var non-empty-string $path */
    protected string $path;
    /** @var non-empty-string $method */
    protected string $method;

    public function __construct(
        ?string $path = null,
        null|array|string $middlewares = null,
        ?string $name = null,
        ?string $method = null
    ) {
        $path && $this->setPath($path);
        $middlewares && $this->setMiddlewares($middlewares);
        $name && $this->setName($name);
        $method && $this->setMethod($method);
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
     * @param class-string[]|class-string $middlewares
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

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param non-empty-string $name
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param non-empty-string $path
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param non-empty-string $method
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function register(Application $app): void
    {
        match ($this->method) {
            RequestMethodInterface::METHOD_DELETE => $app->delete($this->path, $this->middlewares, $this->name),
            RequestMethodInterface::METHOD_GET => $app->get($this->path, $this->middlewares, $this->name),
            RequestMethodInterface::METHOD_PATCH => $app->patch($this->path, $this->middlewares, $this->name),
            RequestMethodInterface::METHOD_POST => $app->post($this->path, $this->middlewares, $this->name),
            RequestMethodInterface::METHOD_PUT => $app->put($this->path, $this->middlewares, $this->name),
            default => null,
        };
    }

    /**
     * @param class-string[]|class-string $middlewares
     */
    public function excludeMiddlewares(array|string $middlewares): self
    {
        if (is_string($middlewares)) {
            $middlewares = [$middlewares];
        }

        foreach ($middlewares as $middleware) {
            if (in_array($middleware, $this->excludedMiddlewares, true)) {
                continue;
            }
            $this->excludedMiddlewares[] = $middleware;
        }

        return $this;
    }

    public function getExcludedMiddlewares(): array
    {
        return $this->excludedMiddlewares;
    }
}
