<?php

namespace MagentoHackathon\Composer\Magento\Directives;

use MagentoHackathon\Composer\Magento\Directives\Action\AbstractAction;

class ArrayDumper implements DumperInterface
{
    /**
     * uses dumper to flatten the bag into multi-dimensional array
     * array(
     *   array('add', 'source', 'dest'),
     * )
     * @param Bag|AbstractAction[] $bag
     * @return array
     */
    public function dump(Bag $bag)
    {
        $res = [];
        foreach ($bag as $action) {
            $res[] = [$action->getType(), $action->getSource(), $action->getDestination()];
        }
        return $res;
    }
}
