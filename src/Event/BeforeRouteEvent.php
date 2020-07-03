<?php declare(strict_types=1);

namespace Tolkam\Routing\Event;

use Psr\Http\Message\ServerRequestInterface;

class BeforeRouteEvent implements RoutingEventInterface
{
    /**
     * @var ServerRequestInterface
     */
    protected ServerRequestInterface $request;
    
    /**
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }
    
    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
