<?php

namespace MagentoHackathon\Composer\Magento\Directives;

use MagentoHackathon\Composer\Magento\Deploystrategy\DeploystrategyAbstract;

interface ActionInterface
{
    /**
     * process current action as part of directions
     * @param DeploystrategyAbstract $strat
     * @return bool
     */
    public function process(DeploystrategyAbstract $strat);
}
