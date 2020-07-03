<?php declare(strict_types=1);

namespace Tolkam\Routing\Runner\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tolkam\Routing\Traits\AssertionsTrait;
use Tolkam\Routing\Traits\RouteHandlerAwareTrait;

class RequestHandlerRunner implements HandlerRunnerInterface
{
    use RouteHandlerAwareTrait;
    use AssertionsTrait;
    
    /**
     * @inheritDoc
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $requestHandler
    ): ResponseInterface {
        
        if ($this->routeHandler instanceof RequestHandlerInterface) {
            $response = $this->routeHandler->handle($request);
            $this->assertValidResponse($response, $this->routeName);
            
            return $response;
        }
        
        return $requestHandler->handle($request);
    }
}
