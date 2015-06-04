<?php

namespace MagentoHackathon\Composer\Magento\Directives;

class Bag implements \IteratorAggregate, \Countable
{
    /**
     * @var ActionInterface[]
     */
    protected $actions;

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
}
