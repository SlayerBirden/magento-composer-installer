<?php

namespace MagentoHackathon\Composer\Magento\Directives\Action;

use MagentoHackathon\Composer\Magento\Directives\ActionInterface;

abstract class AbstractAction implements ActionInterface
{
    /**
     * @var string
     */
    protected $source;
    /**
     * @var string
     */
    protected $destination;

    /**
     * @param string $source
     * @param string $destination
     */
    public function __construct($source, $destination)
    {
        $this->source = $source;
        $this->destination = $destination;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param string $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s: from %s to %s.", $this->getType(), $this->getSource(), $this->getDestination());
    }
}
