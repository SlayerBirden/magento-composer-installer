<?php

namespace MagentoHackathon\Composer\Magento\Event;

use Composer\EventDispatcher\Event;

class DebugEvent extends Event
{
    /**
     * @var string
     */
    private $message;

    /**
     * @param string $name
     * @param string $message
     */
    public function __construct($name, $message)
    {
        parent::__construct($name);
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return (string) $this->message;
    }
}
