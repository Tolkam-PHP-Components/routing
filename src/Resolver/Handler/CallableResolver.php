<?php declare(strict_types=1);

namespace Tolkam\Routing\Resolver\Handler;

use Tolkam\Routing\Resolver\ResolverInterface;

class CallableResolver implements ResolverInterface
{
    /**
     * @inheritDoc
     */
    public function isResolvable($routeHandler): bool
    {
        return is_callable($routeHandler);
    }
    
    /**
     * @inheritDoc
     */
    public function resolve($routeHandler)
    {
        return $routeHandler;
    }
}
