<?php declare(strict_types=1);

namespace Tolkam\Routing;

use Psr\Http\Message\ServerRequestInterface;

interface RouterAdapterInterface
{
    /**
     * @return RouteInterface[]
     */
    public function getRoutes(): array;

    /**
     * Gets route by name or current one if name is not provided
     *
     * @param string|null $name
     *
     * @return RouteInterface
     */
    public function getRoute(string $name = null): RouteInterface;

    /**
     * Matches route against request
     *
     * @param ServerRequestInterface $request
     *
     * @throws Exception
     * @return RouteInterface
     */
    public function match(ServerRequestInterface $request): RouteInterface;

    /**
     * Builds a URI from the route instance
     *
     * @param string $name       Route name
     * @param array  $parameters Route parameters
     * @param array  $options    Extra options required by the underlying router
     *
     * @return string
     */
    public function generate(string $name, array $parameters = [], array $options = []): string;
}
