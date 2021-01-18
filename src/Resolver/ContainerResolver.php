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
    public function isResolvable($value): bool
    {
        return is_string($value) && $this->container->has($value);
    }
    
    /**
     * @inheritDoc
     */
    public function resolve($value)
    {
        return $this->container->get($value);
    }
}
