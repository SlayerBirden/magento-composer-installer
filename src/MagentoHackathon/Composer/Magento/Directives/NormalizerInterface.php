<?php

namespace MagentoHackathon\Composer\Magento\Directives;

interface NormalizerInterface
{
    /**
     * @param Bag|ActionInterface[] $bag
     * @return array
     */
    public function normalize(Bag $bag);
}
