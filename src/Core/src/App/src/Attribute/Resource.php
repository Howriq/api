<?php

declare(strict_types=1);

namespace Core\App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class Resource implements ResourceInterface
{
    /** @param class-string $class */
    public function __construct(
        public string $class,
        public string $identifier = 'uuid',
        public string $placeholder = 'uuid',
    ) {
    }
}
