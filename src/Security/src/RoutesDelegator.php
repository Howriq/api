<?php

declare(strict_types=1);

namespace Api\Security;

use Api\App\Route\Delete;
use Api\App\Route\Get;
use Api\App\Route\Route;
use Api\App\Route\RouteGroup;
use Api\Security\Middleware\ErrorResponseMiddleware;
use Fig\Http\Message\RequestMethodInterface;
use Mezzio\Application;
use Mezzio\Authentication\OAuth2\TokenEndpointHandler;
use Psr\Container\ContainerInterface;

use function assert;

class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $callback): Application
    {
        $app = $callback();
        assert($app instanceof Application);

        $app->post(
            '/security/token',
            [ErrorResponseMiddleware::class, TokenEndpointHandler::class],
            'security::token'
        );

        (new Delete(
            '/route/delete/{uuid}',
            [ErrorResponseMiddleware::class, TokenEndpointHandler::class],
            'route::delete'
        )
        )->register($app);

        (new Get())
            ->setPath('/route/get')
            ->setMiddlewares([ErrorResponseMiddleware::class, TokenEndpointHandler::class])
            ->setName('route::get')
            ->register($app);

        (new Route(
            '/route/patch',
            [ErrorResponseMiddleware::class, TokenEndpointHandler::class],
            'route::patch',
            RequestMethodInterface::METHOD_POST
        )
        )->register($app);

        (new Route())
            ->setPath('/route/post')
            ->setMiddlewares([ErrorResponseMiddleware::class, TokenEndpointHandler::class])
            ->setName('route::post')
            ->setMethod(RequestMethodInterface::METHOD_POST)
            ->register($app);

        (new Route())
            ->setPath('/route/put')
            ->setMiddlewares([ErrorResponseMiddleware::class, TokenEndpointHandler::class])
            ->setName('route::pout')
            ->setMethod(RequestMethodInterface::METHOD_PUT)
            ->excludeMiddlewares(ErrorResponseMiddleware::class)
            ->register($app);

        (new RouteGroup('/group', ErrorResponseMiddleware::class))
            ->addRoute(
                new Delete('/delete', TokenEndpointHandler::class, 'group::delete')
            )
            ->addRoute(
                (new Get())->setPath('/get')->setMiddlewares(TokenEndpointHandler::class)->setName('group::get')
            )
            ->addRoute(
                new Route('/patch', TokenEndpointHandler::class, 'group::patch', RequestMethodInterface::METHOD_PATCH)
            )
            ->addRoute(
                (new Route())
                    ->setPath('/post')
                    ->setMiddlewares(TokenEndpointHandler::class)
                    ->setName('group::post')
                    ->setMethod(RequestMethodInterface::METHOD_POST)
            )
            ->addRoute(
                (new Route('/put', TokenEndpointHandler::class, 'group::put', RequestMethodInterface::METHOD_PUT))
                    ->excludeMiddlewares(ErrorResponseMiddleware::class)
            )
            ->register($app);

        return $app;
    }
}
