<?php declare(strict_types=1);

namespace Tolkam\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tolkam\Routing\Event\BeforeMatchEvent;
use Tolkam\Routing\Traits\EventDispatcherAwareTrait;

class MatcherMiddleware implements MiddlewareInterface
{
    use EventDispatcherAwareTrait;

    /**
     * @param RouterAdapterInterface $adapter
     */
    public function __construct(private readonly RouterAdapterInterface $adapter)
    {
    }

    /**
     * @inheritDoc
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        return $handler->handle($this->match($request));
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ServerRequestInterface
     * @throws Exception
     */
    private function match(ServerRequestInterface $request): ServerRequestInterface
    {
        if (empty($this->adapter->getRoutes())) {
            throw new Exception('No routes defined');
        }

        if ($eventDispatcher = $this->getEventDispatcher()) {
            $eventDispatcher->dispatch(new BeforeMatchEvent($request));
        }

        $route = $this->adapter->match($request);

        foreach ($route->getAttributes() as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        return $request->withAttribute(RouteInterface::class, $route);
    }
}
