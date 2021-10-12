<?php
namespace Tolkam\Routing\Exception;

use Tolkam\Routing\Exception;

class NotAllowedException extends Exception
{
    /**
     * Allowed verbs
     * @var array
     */
    protected array $allowed = [];

    /**
     * Sets the allowed verbs
     *
     * @param array $allowed
     * @return self
     */
    public function setAllowed(array $allowed): NotAllowedException
    {
        $this->allowed = $allowed;
        return $this;
    }

    /**
     * Gets the allowed verbs
     *
     * @return array
     */
    public function getAllowed(): array
    {
        return $this->allowed;
    }
}
