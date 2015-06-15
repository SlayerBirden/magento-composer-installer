<?php

namespace MagentoHackathon\Composer\Magento\Directives;

interface DumperInterface
{
    /**
     * @param Bag $bag
     */
    public function dump(Bag $bag);
}
