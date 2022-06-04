<?php declare(strict_types=1);

namespace Tolkam\Routing\Event;

use Psr\Http\Message\ServerRequestInterface;

class BeforeMatchEvent
{
    /**
     * @param ServerRequestInterface $request
     */
    public function __construct(private readonly ServerRequestInterface $request)
    {
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
