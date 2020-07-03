<?php declare(strict_types=1);

namespace Tolkam\Routing\Runner\Handler;

use Psr\Http\Server\MiddlewareInterface;

/**
 * Executor is middleware itself
 *
 * @package Tolkam\Routing\RoutingStrategy
 */
interface HandlerRunnerInterface extends MiddlewareInterface
{
    /**
     * Sets resolved route handler
     *
     * @param        $routeHandler
     * @param string $routeName
     *
     * @return mixed
     */
    public function useHandler($routeHandler, string $routeName);
}
