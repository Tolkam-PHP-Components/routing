<?php declare(strict_types=1);

namespace Tolkam\Routing\Resolver;

use Psr\Container\ContainerInterface;

class ContainerResolver implements ResolverInterface
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;
    
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    /**
     * @inheritDoc
     */
    public function isResolvable($routeHandler): bool
    {
        return is_string($routeHandler) && $this->container->has($routeHandler);
    }
    
    /**
     * @inheritDoc
     */
    public function resolve($routeHandler)
    {
        return $this->container->get($routeHandler);
    }
}
