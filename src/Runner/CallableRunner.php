<?php declare(strict_types=1);

namespace Tolkam\Routing\Runner;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CallableRunner implements RunnerInterface
{
    use RouteHandlerAwareTrait;
    use AssertionsTrait;

    /**
     * @inheritDoc
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $arrayCallable = is_array($this->routeHandler);

        if ($arrayCallable) {
            [$class, $method] = $this->routeHandler;
            $isCallable = method_exists((string) $class, (string) $method);
        }
        else {
            $isCallable = is_callable($this->routeHandler);
        }

        if ($isCallable) {
            $response = $arrayCallable
                ? (new $class())->$method($request)
                : call_user_func($this->routeHandler, $request);

            $this->assertValidResponse($response, $this->routeName);

            return $response;
        }

        return $handler->handle($request);
    }
}
