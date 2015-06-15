<?php

namespace MagentoHackathon\Composer\Magento\Directives\Action;

use MagentoHackathon\Composer\Magento\Deploystrategy\DeploystrategyAbstract;
use MagentoHackathon\Composer\Magento\Directives\ActionInterface;

class Update extends AbstractAction implements ActionInterface
{
    const TYPE = 'update';

    /**
     * {@inheritdoc}
     */
    public function process(DeploystrategyAbstract $strat)
    {
        $strat->remove($this->source, $this->destination);
        return $strat->create($this->source, $this->destination);
    }
}
