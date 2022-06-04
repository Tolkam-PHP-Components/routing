<?php declare(strict_types=1);

namespace Tolkam\Routing\Runner;

trait RouteHandlerAwareTrait
{
    /**
     * @var mixed
     */
    private mixed $routeHandler;

    /**
     * @var string
     */
    private string $routeName;

    /**
     * @param mixed  $routeHandler
     * @param string $routeName
     *
     * @return void
     */
    public function setHandler(mixed $routeHandler, string $routeName): void
    {
        $this->routeHandler = $routeHandler;
        $this->routeName = $routeName;
    }
}
