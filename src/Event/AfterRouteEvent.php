<?php declare(strict_types=1);

namespace Tolkam\Routing\Event;

use Tolkam\Routing\Route;

class AfterRouteEvent implements RoutingEventInterface
{
    /**
     * @var Route
     */
    private Route $route;
    
    /**
     * @param Route $route
     */
    public function __construct(Route $route)
    {
        $this->route = $route;
    }
    
    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }
}
