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

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getDestination();

    /**
     * @return string
     */
    public function getSource();
}
