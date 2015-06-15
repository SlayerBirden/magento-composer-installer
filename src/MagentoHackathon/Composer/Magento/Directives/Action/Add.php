<?php

namespace MagentoHackathon\Composer\Magento\Directives\Action;

use MagentoHackathon\Composer\Magento\Deploystrategy\DeploystrategyAbstract;
use MagentoHackathon\Composer\Magento\Directives\ActionInterface;

class Add extends AbstractAction implements ActionInterface
{
    const TYPE = 'add';

    /**
     * {@inheritdoc}
     */
    public function process(DeploystrategyAbstract $strat)
    {
        return $strat->create($this->source, $this->destination);
    }
}
