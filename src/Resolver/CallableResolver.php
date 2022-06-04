<?php declare(strict_types=1);

namespace Tolkam\Routing\Resolver;

class CallableResolver implements ResolverInterface
{
    /**
     * @inheritDoc
     */
    public function isResolvable($value): bool
    {
        if (is_array($value)) {
            [$class, $method] = $value;

            return method_exists((string) $class, (string) $method);
        }

        return is_callable($value);
    }

    /**
     * @inheritDoc
     */
    public function resolve($value): mixed
    {
        return $value;
    }
}
