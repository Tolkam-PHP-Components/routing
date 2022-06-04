<?php declare(strict_types=1);

namespace Tolkam\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tolkam\PSR15\Dispatcher\Dispatcher;
use Tolkam\Routing\Resolver\ResolverInterface;
use Tolkam\Routing\Runner\RunnerInterface;

class DispatcherMiddleware implements MiddlewareInterface, RequestHandlerInterface
{
    /**
     * @var ResolverInterface[]
     */
    private array $resolvers = [];

    /**
     * @var RunnerInterface[]
     */
    private array $runners = [];

    /**
     * @var RouteInterface|null
     */
    private ?RouteInterface $route = null;

    /**
     * @inheritDoc
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $this->setRoute($request->getAttribute(RouteInterface::class));

        // using getter for validation
        $route = $this->getRoute();
        $routeName = $route->getName();

        // resolve individual route middlewares
        $routeMiddlewares = $this->resolveMiddlewares($route->getMiddlewares(), $routeName);

        // provide each runner with resolved handler
        $handler = $this->resolveHandler($route->getHandler(), $routeName);
        foreach ($this->runners as $runner) {
            $runner->setHandler($handler, $routeName);
        }

        // use self as fallback runner
        return Dispatcher::create($this)
            ->middlewares($routeMiddlewares)
            ->middlewares($this->runners)
            ->handle($request);
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $this->getRoute();

        throw new Exception(sprintf(
            'None of the runners were able to run a handler for "%s" route',
            $route->getName()
        ));
    }

    /**
     * Adds resolver
     *
     * @param ResolverInterface $resolver
     *
     * @return self
     */
    public function addResolver(ResolverInterface $resolver): self
    {
        $this->resolvers[] = $resolver;

        return $this;
    }

    /**
     * Adds handler runner
     *
     * @param RunnerInterface $runner
     *
     * @return self
     */
    public function addRunner(RunnerInterface $runner): self
    {
        $this->runners[] = $runner;

        return $this;
    }

    /**
     * Sets the route explicitly
     *
     * @param RouteInterface|null $route
     */
    public function setRoute(?RouteInterface $route): void
    {
        $this->route = $route;
    }

    /**
     * Gets the route
     *
     * @return RouteInterface
     */
    public function getRoute(): RouteInterface
    {
        if (!$this->route) {
            throw new Exception(sprintf(
                'No matched route found. Have you forgotten to add or configure %s?',
                MatcherMiddleware::class
            ));
        }

        return $this->route;
    }

    /**
     * Resolves middlewares array
     *
     * @param array  $values
     * @param string $routeName
     *
     * @return array
     */
    private function resolveMiddlewares(array $values, string $routeName): array
    {
        $resolved = [];
        foreach ($values as $value) {
            $middleware = $value;
            if (!$middleware instanceof MiddlewareInterface) {
                $middleware = $this->resolveMiddleware($value, $routeName);
            }
            $middleware = !is_array($middleware) ? [$middleware] : $middleware;

            foreach ($middleware as $item) {
                if (!($item instanceof MiddlewareInterface)) {
                    throw new Exception(sprintf(
                        'Each middleware for "%1$s" route must be an instance of %2$s, %3$s given',
                        $routeName,
                        MiddlewareInterface::class,
                        gettype($item)
                    ));
                }
            }

            $resolved[] = $middleware;
        }

        return call_user_func_array('array_merge', $resolved);
    }

    /**
     * Resolves value into middleware
     *
     * @param string $routeName
     * @param        $value
     *
     * @return array
     */
    private function resolveMiddleware($value, string $routeName): array
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->isResolvable($value)) {
                $resolved = $resolver->resolve($value);

                return is_array($resolved) ? $resolved : [$resolved];
            }
        }

        throw new Exception(sprintf(
            'None of the resolvers was able to resolve "%s" middleware for the "%s" route',
            is_string($value) ? $value : gettype($value),
            $routeName
        ));
    }

    /**
     * Resolves handler suitable to be run by one of the registered runners
     *
     * @param        $value
     * @param string $routeName
     *
     * @return mixed
     */
    private function resolveHandler($value, string $routeName): mixed
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->isResolvable($value)) {
                return $resolver->resolve($value);
            }
        }

        throw new Exception(sprintf(
            'None of the resolvers was able to resolve "%s" handler for the "%s" route',
            is_string($value) ? $value : gettype($value),
            $routeName
        ));
    }
}
