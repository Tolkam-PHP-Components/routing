<?php declare(strict_types=1);

namespace Tolkam\Routing\Traits;

trait RouteHandlerAwareTrait
{
    /**
     * @var mixed
     */
    protected $routeHandler;
    
    /**
     * @var string
     */
    protected string $routeName;
    
    /**
     * @inheritDoc
     */
    public function useHandler($routeHandler, string $routeName)
    {
        $this->routeHandler = $routeHandler;
        $this->routeName = $routeName;
    }
}
