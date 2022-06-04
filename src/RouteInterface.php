<?php declare(strict_types=1);

namespace Tolkam\Routing;

interface RouteInterface
{
    /**
     * Gets route name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Gets route handler resolvable definition
     *
     * This definition should be possible to resolve with one of the provided resolvers,
     * and run with one of the provided runners
     *
     * @return mixed
     */
    public function getHandler(): mixed;

    /**
     * Gets route middlewares resolvable definitions
     *
     * Array of definitions (like strings, arrays, etc.)
     * that should be possible to resolve with provided resolvers
     *
     * @return array
     */
    public function getMiddlewares(): array;

    /**
     * Gets route attributes
     *
     * Map of attributes parsed from route URI [param => value]
     * Like [id => 1, slug => my-slug]
     *
     * @return string[]
     */
    public function getAttributes(): array;

    /**
     * Gets the original route
     *
     * @return object
     */
    public function getOriginalRoute(): object;
}
