<?php

namespace MagentoHackathon\Composer\Magento\Directives;

use MagentoHackathon\Composer\Magento\Directives\Action\AbstractAction;

class SimpleNormalizer implements NormalizerInterface
{
    /**
     * return array of Actions - but enhanced:
     * remove excluded entries:
     *   add file (removed)
     *   remove file (removed)
     * remove unnecessary updates:
     *   update file (removed)
     *   remove file
     * @param Bag|AbstractAction[] $bag
     * {@inheritdoc}
     */
    public function normalize(Bag $bag)
    {
        $targets = [];
        foreach ($bag as $action) {
            $canAdd = true;
            if (isset($targets[$action->getDestination()])) {
                $canAdd = false;
                // now it's interesting
                /** @var AbstractAction $previous */
                $previous = $targets[$action->getDestination()];
                $new = $action;
                if (in_array($previous->getType(), ['add', 'create']) &&
                    in_array($new->getType(), ['delete', 'remove'])) {
                    // something got added and then removed
                    // unset prev entry
                    // not add new one
                    unset($targets[$action->getDestination()]);
                } elseif ($previous->getType() == 'update' &&
                    in_array($new->getType(), ['delete', 'remove'])) {
                    // something got updated and then removed
                    // we don't need to perform update - nor can we
                    unset($targets[$action->getDestination()]);
                    // but add delete
                    $canAdd = true;
                }
            }
            if ($canAdd) {
                $targets[$action->getDestination()] = $action;
            }
        }
        return array_values($targets);
    }
}
