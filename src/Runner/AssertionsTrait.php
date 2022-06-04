<?php

namespace Tolkam\Routing\Runner;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

trait AssertionsTrait
{
    /**
     * Validates response
     *
     * @param        $response
     * @param string $routeName
     *
     * @throws RuntimeException
     */
    public function assertValidResponse($response, string $routeName): void
    {
        if (!($response instanceof ResponseInterface)) {
            throw new RuntimeException(sprintf(
                'Value returned by runner of "%1$s" route must be an instance of %2$s, %3$s returned',
                $routeName,
                ResponseInterface::class,
                gettype($response)
            ));
        }
    }
}
