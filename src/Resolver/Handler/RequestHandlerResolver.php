<?php declare(strict_types=1);

namespace Tolkam\Routing\Resolver\Handler;

use Psr\Http\Server\RequestHandlerInterface;
use Tolkam\Routing\Resolver\ResolverInterface;

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
    public function resolve($value)
    {
        return $value;
    }
}
