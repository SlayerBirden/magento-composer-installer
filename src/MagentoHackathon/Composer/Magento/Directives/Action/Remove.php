<?php

namespace MagentoHackathon\Composer\Magento\Directives\Action;

use MagentoHackathon\Composer\Magento\Deploystrategy\DeploystrategyAbstract;
use MagentoHackathon\Composer\Magento\Directives\ActionInterface;

class Remove implements ActionInterface
{
    /**
     * @var string
     */
    private $source;
    /**
     * @var string
     */
    private $destination;

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
     * {@inheritdoc}
     */
    public function process(DeploystrategyAbstract $strat)
    {
        $strat->remove($this->source, $this->destination);
        return true;
    }
}
