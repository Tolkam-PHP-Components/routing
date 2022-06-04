<?php declare(strict_types=1);

namespace Tolkam\Routing\Resolver;

interface ResolverInterface
{
    /**
     * Checks if resolver is able to resolve
     *
     * @param $value
     *
     * @return bool
     */
    public function isResolvable($value): bool;

    /**
     * Returns resolved object
     *
     * @param $value
     *
     * @return mixed
     */
    public function resolve($value): mixed;
}
