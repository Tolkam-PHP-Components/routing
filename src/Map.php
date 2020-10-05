<?php declare(strict_types=1);

namespace Tolkam\Routing;

/**
 * @method Route accepts(string|array $accepts)
 *
 * @method Route allows(string|array $allows)
 *
 * @method Route attributes(array $attributes)
 *
 * @method Route auth(mixed $auth)
 *
 * @method Route defaults(array $defaults)
 *
 * @method Route extras(array $extras)
 *
 * @method Route failedRule(mixed $failedRule)
 *
 * @method Route handler(mixed $handler)
 *
 * @method Route host(mixed $host)
 *
 * @method Route isRoutable(bool $isRoutable = true)
 *
 * @method Route namePrefix(string $namePrefix)
 *
 * @method Route path(string $path)
 *
 * @method Route pathPrefix(string $pathPrefix)
 *
 * @method Route secure(bool|null $secure = true)
 *
 * @method Route special(callable|null $host)
 *
 * @method Route tokens(array $tokens)
 *
 * @method Route wildcard(string $wildcard)
 *
 * @method Route middlewares(array $middlewares)
 */
class Map extends \Aura\Router\Map
{
}
