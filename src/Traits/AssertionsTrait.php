<?php declare(strict_types=1);

namespace Tolkam\Routing\Traits;

use Psr\Http\Message\ResponseInterface;
use Tolkam\Routing\Runner\HandlerRunnerException;

trait AssertionsTrait
{
    /**
     * Validates response
     *
     * @param        $response
     * @param string $routeName
     *
     * @throws HandlerRunnerException
     */
    public function assertValidResponse($response, string $routeName)
    {
        if (!($response instanceof ResponseInterface)) {
            throw new HandlerRunnerException(sprintf(
                'Handler returned value for "%1$s" route must be an instance of %2$s, %3$s returned',
                $routeName,
                ResponseInterface::class,
                gettype($response)
            ));
        }
    }
}
