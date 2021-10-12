<?php declare(strict_types=1);

namespace Tolkam\Routing;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tolkam\PSR15\Dispatcher\Dispatcher;
use Tolkam\Routing\Event\BeforeRouteEvent;
use Tolkam\Routing\Resolver\ResolverInterface;
use Tolkam\Routing\Runner\HandlerRunnerException;
use Tolkam\Routing\Runner\HandlerRunnerInterface;
use Tolkam\Routing\Traits\AssertionsTrait;

class RoutingMiddleware implements RequestHandlerInterface, MiddlewareInterface
{
    use AssertionsTrait;
    
    /**
     * @var RouterContainer
     */
    protected RouterContainer $routerContainer;
    
    /**
     * @var ResolverInterface[]
     */
    protected array $middlewareResolvers = [];
    
    /**
     * @var ResolverInterface[]
     */
    protected array $handlerResolvers = [];
    
    /**
     * @var HandlerRunnerInterface[]
     */
    protected array $handlerRunners = [];
    
    /**
     * @var EventDispatcherInterface|null
     */
    protected ?EventDispatcherInterface $eventDispatcher = null;
    
    /**
     * @var Route|null
     */
    protected ?Route $matchedRoute = null;
    
    /**
     * @param RouterContainer $routerContainer
     */
    public function __construct(RouterContainer $routerContainer)
    {
        $this->routerContainer = $routerContainer;
    }
    
    /**
     * Adds middleware resolver
     *
     * @param ResolverInterface $resolver
     *
     * @return self
     */
    public function addMiddlewareResolver(ResolverInterface $resolver): self
    {
        $this->middlewareResolvers[] = $resolver;
        
        return $this;
    }
    
    /**
     * Adds route handler resolver
     *
     * @param ResolverInterface $resolver
     *
     * @return self
     */
    public function addHandlerResolver(ResolverInterface $resolver): self
    {
        $this->handlerResolvers[] = $resolver;
        
        return $this;
    }
    
    /**
     * Adds handler runner
     *
     * @param HandlerRunnerInterface $runner
     *
     * @return self
     */
    public function addRunner(HandlerRunnerInterface $runner): self
    {
        $this->handlerRunners[] = $runner;
        
        return $this;
    }
    
    /**
     * Gets the event dispatcher
     *
     * @return EventDispatcherInterface|null
     */
    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }
    
    /**
     * Sets the event dispatcher
     *
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return self
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): self
    {
        $this->eventDispatcher = $eventDispatcher;
        
        return $this;
    }
    
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler - Not used. The middleware short-circuits
     *                                         to self and throws on errors.
     *
     * @return ResponseInterface
     * @throws Exception
     * @throws Exception\NotAcceptedException
     * @throws Exception\NotAllowedException
     * @throws Exception\NotFoundException
     * @throws HandlerRunnerException
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        
        if (!$this->hasRoutes()) {
            throw new Exception('No routes defined');
        }
        
        if ($eventDispatcher = $this->getEventDispatcher()) {
            $eventDispatcher->dispatch(new BeforeRouteEvent($request));
        }
        
        // store current route
        $this->matchedRoute = $this->match($request);
        $matchedRouteName = $this->matchedRoute->name;
        
        // add route to request
        $request = $request->withAttribute(Route::class, $this->matchedRoute);
        
        // resolve individual route middlewares
        $routeMiddlewares = $this->resolveMiddlewares(
            $this->matchedRoute->middlewares ?: $this->matchedRoute->extras['middlewares'] ?? [],
            $matchedRouteName
        );
        
        // add route attributes to request
        foreach ($this->matchedRoute->attributes as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }
        
        // provide each runner with resolved handler
        foreach ($this->handlerRunners as $runner) {
            $runner->useHandler(
                $this->resolveHandler($this->matchedRoute->handler, $matchedRouteName),
                $matchedRouteName
            );
        }
        
        // use $this as fallback runner
        return Dispatcher::create($this)
            ->middlewares($routeMiddlewares)
            ->middlewares($this->handlerRunners)
            ->handle($request);
    }
    
    /**
     * Fallback runner
     *
     * @param ServerRequestInterface $request
     *
     * @throws Exception
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->matchedRoute) {
            throw new Exception('No route matched, nothing to handle');
        }
        
        throw new Exception(sprintf(
            'Handler for "%s" was resolved, but none of the '
            . 'runners were able to run it',
            $this->matchedRoute->name
        ));
    }
    
    /**
     * Resolves middlewares array
     *
     * @param array  $values
     * @param string $routeName
     *
     * @return array
     */
    protected function resolveMiddlewares(array $values, string $routeName): array
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
                    throw new HandlerRunnerException(sprintf(
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
    protected function resolveMiddleware($value, string $routeName): array
    {
        foreach ($this->middlewareResolvers as $resolver) {
            if ($resolver->isResolvable($value)) {
                return $resolver->resolve($value);
            }
        }
        
        throw new Exception(sprintf(
            'None of the resolvers was able to resolve "%s" middleware for the "%s" route',
            is_string($value) ? $value : gettype($value),
            $routeName
        ));
    }
    
    /**
     * Resolves handler suitable to be run by the one of registered runners
     *
     * @param        $value
     * @param string $routeName
     *
     * @return mixed
     */
    protected function resolveHandler($value, string $routeName)
    {
        foreach ($this->handlerResolvers as $resolver) {
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
    
    /**
     * Finds matched route
     *
     * @param ServerRequestInterface $request
     *
     * @throws Exception
     * @throws Exception\NotAcceptedException
     * @throws Exception\NotAllowedException
     * @throws Exception\NotFoundException
     * @return Route
     */
    protected function match(ServerRequestInterface $request): Route
    {
        $matcher = $this->routerContainer->getMatcher();
        $route = $matcher->match($request);
        
        // not matched
        if ($route === false) {
            $failed = $matcher->getFailedRoute();
            $uri = $request->getUri();
            
            switch ($failed->failedRule) {
                case 'Aura\Router\Rule\Allows':
                    $e = new Exception\NotAllowedException(sprintf(
                        'Method "%s" is not allowed for "%s"',
                        $request->getMethod(),
                        $uri
                    ));
                    $e->setAllowed($failed->allows);
                    throw $e;
                case 'Aura\Router\Rule\Accepts':
                    throw new Exception\NotAcceptedException(sprintf(
                        '"%s" is not able to respond with the accepted content type "%s"',
                        $uri,
                        $request->getHeaderLine('Accept')
                    ));
            }
            
            throw new Exception\NotFoundException(sprintf('No matching rule found for "%s"', $uri));
        }
        
        /** @var Route $route */
        return $route;
    }
    
    /**
     * @return bool
     */
    protected function hasRoutes(): bool
    {
        return !empty($this->routerContainer->getMap()->getRoutes());
    }
}
