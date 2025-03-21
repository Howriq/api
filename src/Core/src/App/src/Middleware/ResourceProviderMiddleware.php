<?php

declare(strict_types=1);

namespace Core\App\Middleware;

use Api\App\Exception\NotFoundException;
use Api\App\Service\HandlerService;
use Core\App\Attribute\Resource;
use Core\App\Message;
use Doctrine\ORM\EntityManagerInterface;
use Dot\DependencyInjection\Attribute\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionClass;
use ReflectionException;

use function array_pop;
use function count;
use function explode;
use function sprintf;

readonly class ResourceProviderMiddleware implements MiddlewareInterface
{
    #[Inject(EntityManagerInterface::class)]
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $reflectionHandler = HandlerService::fromRequest($request);
        if (! $reflectionHandler instanceof ReflectionClass) {
            return $handler->handle($request);
        }

        $reflectionAttributes = $reflectionHandler->getMethod('handle')->getAttributes(Resource::class);
        if (count($reflectionAttributes) === 0) {
            return $handler->handle($request);
        }

        /** @var Resource $resource */
        $resource = $reflectionAttributes[0]->newInstance();

        $entity = $this->entityManager->getRepository($resource->class)->findOneBy([
            $resource->identifier => $request->getAttribute($resource->placeholder),
        ]);
        if ($entity === null) {
            $entity = explode('\\', $resource->class);
            throw new NotFoundException(
                sprintf(Message::RESOURCE_NOT_FOUND, array_pop($entity)),
            );
        }

        return $handler->handle($request->withAttribute(Resource::class, $entity));
    }
}
