<?php

namespace MagentoHackathon\Composer\Magento\Directives;

class Bag implements \IteratorAggregate, \Countable
{
    /**
     * @var ActionInterface[]
     */
    protected $actions = [];
    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->actions);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->actions);
    }

    /**
     * @param ActionInterface $action
     * @return $this
     */
    public function add(ActionInterface $action)
    {
        $this->actions[] = $action;
        return $this;
    }

    /**
     * return diff between current bag and other bag
     * throws exception if both Bags begin with different Actions
     * @param Bag|ActionInterface[] $bag
     * @return $this
     * @throws \Exception
     */
    public function diff(Bag $bag)
    {
        $i = 0;
        foreach ($bag as $action) {
            if ($action != $this->actions[$i]) {
                throw new \Exception(sprintf("Can not diff bags, different actions on #%d", $i + 1));
            } else {
                unset($this->actions[$i]);
            }
            $i++;
        }
        // reindex
        $this->actions = array_values($this->actions);
        return $this;
    }

    /**
     * @return $this|array|ActionInterface[]
     */
    public function getNormalized()
    {
        return $this->normalizer->normalize($this);
    }
}
