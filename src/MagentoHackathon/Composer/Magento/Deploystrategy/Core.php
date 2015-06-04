<?php

namespace MagentoHackathon\Composer\Magento\Deploystrategy;

use MagentoHackathon\Composer\Magento\Directives\ActionInterface;
use MagentoHackathon\Composer\Magento\Directives\Bag;

class Core extends Copy
{
    /** @var Bag|ActionInterface[] */
    protected $actionBag;

    /**
     * @return Bag|ActionInterface[]
     */
    public function getActionBag()
    {
        return $this->actionBag;
    }

    /**
     * @param Bag $actionBag
     */
    public function setActionBag($actionBag)
    {
        $this->actionBag = $actionBag;
    }

    /**
     * {@inheritdoc}
     */
    public function deploy()
    {
        $this->beforeDeploy();
        foreach ($this->actionBag as $action) {
            $action->process($this);
        }
        $this->afterDeploy();
        return $this;
    }

    /**
     * {@inheritdoc}
     * core installer does not need cleaning
     */
    public function clean()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     * don't add deployed files
     */
    public function addDeployedFile($file)
    {
        return;#pass
    }
}
