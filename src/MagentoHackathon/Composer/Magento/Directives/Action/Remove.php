<?php

namespace MagentoHackathon\Composer\Magento\Directives\Action;

use MagentoHackathon\Composer\Magento\Deploystrategy\DeploystrategyAbstract;
use MagentoHackathon\Composer\Magento\Directives\ActionInterface;

class Remove extends AbstractAction implements ActionInterface
{
    const TYPE = 'remove';

    /**
     * {@inheritdoc}
     */
    public function process(DeploystrategyAbstract $strat)
    {
        $strat->remove($this->source, $this->destination);
        return true;
    }
}
