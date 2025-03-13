<?php

declare(strict_types=1);

namespace Api\App\Route;

use Fig\Http\Message\RequestMethodInterface;

class Post extends AbstractRoute
{
    public function __construct(?string $path = null, null|array|string $handlers = null, ?string $name = null)
    {
        parent::__construct($path, $handlers, $name, RequestMethodInterface::METHOD_POST);
    }
}
