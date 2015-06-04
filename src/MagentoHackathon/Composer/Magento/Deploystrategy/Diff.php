<?php

namespace MagentoHackathon\Composer\Magento\Deploystrategy;

class Diff extends Copy
{
    /**
     * {@inheritdoc}
     */
    public function deploy()
    {
        $this->beforeDeploy();
        foreach ($this->actionBag->getNormalized() as $action) {
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
