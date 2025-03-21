<?php

declare(strict_types=1);

namespace Api\Admin\Handler\Admin;

use Api\Admin\Service\AdminServiceInterface;
use Api\App\Handler\AbstractHandler;
use Core\Admin\Entity\Admin;
use Core\App\Attribute\Resource;
use Dot\DependencyInjection\Attribute\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetAdminResourceHandler extends AbstractHandler
{
    #[Inject(
        AdminServiceInterface::class,
    )]
    public function __construct(
        protected AdminServiceInterface $adminService,
    ) {
    }

    #[Resource(class: Admin::class)]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->createResponse(
            $request,
            $request->getAttribute(Resource::class)
        );
    }
}
