<?php

namespace Tolkam\Routing\Traits;

use Psr\EventDispatcher\EventDispatcherInterface;

trait EventDispatcherAwareTrait
{
    /**
     * @var EventDispatcherInterface|null
     */
    private ?EventDispatcherInterface $eventDispatcher = null;

    /**
     * Gets the eventDispatcher
     *
     * @return EventDispatcherInterface|null
     */
    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * Sets the eventDispatcher
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
