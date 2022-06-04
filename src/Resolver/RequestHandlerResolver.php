<?php declare(strict_types=1);

namespace Tolkam\Routing\Resolver;

use Psr\Http\Server\RequestHandlerInterface;

class RequestHandlerResolver implements ResolverInterface
{
    /**
     * @inheritDoc
     */
    public function isResolvable($value): bool
    {
        return $value instanceof RequestHandlerInterface;
    }

    /**
     * @inheritDoc
     */
    public function resolve($value): mixed
    {
        return $value;
    }
}
