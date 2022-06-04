<?php declare(strict_types=1);

namespace Tolkam\Routing\Runner;

use Psr\Http\Server\MiddlewareInterface;

interface RunnerInterface extends MiddlewareInterface
{
    /**
     * Sets resolved route handler
     *
     * @param mixed  $routeHandler
     * @param string $routeName
     *
     * @return void
     */
    public function setHandler(mixed $routeHandler, string $routeName): void;
}
