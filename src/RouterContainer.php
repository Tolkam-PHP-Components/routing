<?php declare(strict_types=1);

namespace Tolkam\Routing;

class RouterContainer extends \Aura\Router\RouterContainer
{
    /**
     * @inheritDoc
     */
    protected function routeFactory()
    {
        return new Route();
    }

    /**
     * @inheritDoc
     */
    protected function mapFactory()
    {
        return new Map($this->getRoute());
    }
}
