<?php declare(strict_types=1);

namespace Tolkam\Routing;

/**
 * @property-read array $middlewares PSR-15 middlewares
 */
class Route extends \Aura\Router\Route
{
    /**
     * @var array
     */
    protected array $middlewares = [];
    
    /**
     * @param array $middlewares
     *
     * @return Route
     */
    public function middlewares(array $middlewares): Route
    {
        $this->middlewares = array_merge($this->middlewares, $middlewares);
        
        return $this;
    }
}
