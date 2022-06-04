<?php declare(strict_types=1);

namespace Tolkam\Routing\Runner;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandlerRunner implements RunnerInterface
{
    use RouteHandlerAwareTrait;

    /**
     * @inheritDoc
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        if ($this->routeHandler instanceof RequestHandlerInterface) {
            return $this->routeHandler->handle($request);
        }

        return $handler->handle($request);
    }
}
