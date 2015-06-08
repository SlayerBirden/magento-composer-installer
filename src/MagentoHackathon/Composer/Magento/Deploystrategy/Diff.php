<?php

namespace MagentoHackathon\Composer\Magento\Deploystrategy;

use MagentoHackathon\Composer\Magento\Directives\ActionInterface;
use MagentoHackathon\Composer\Magento\Event\DebugEvent;

class Diff extends DeploystrategyAbstract
{
    /**
     * {@inheritdoc}
     */
    public function deploy()
    {
        $this->beforeDeploy();
        foreach ($this->actionBag->getNormalized() as $action) {
            $action->process($this);
            $this->getEventManager()->dispatch(new DebugEvent('debug', $action));
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
     */
    public function createDelegate($source, $dest)
    {
        $sourcePath = $this->getSourceDir() . '/' . $this->removeTrailingSlash($source);
        $destPath = $this->getDestDir() . '/' . $this->removeTrailingSlash($dest);
        if (!is_file($sourcePath)) {
            throw new \ErrorException("Diff strategy can only accept files.");
        }

        // Create all directories up to one below the target if they don't exist
        $destDir = dirname($destPath);
        if (!file_exists($destDir)) {
            mkdir($destDir, 0777, true);
        }
        if (file_exists($destPath)) {
            throw new \ErrorException(sprintf("Target %s already exists. Can't use force for diff strategy.", $dest));
        }
        return copy($sourcePath, $destPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getDeployedFiles()
    {
        $filtered = array_filter($this->actionBag->getNormalized(), function (ActionInterface $action) {
            return $action->getType() === 'add';
        });
        return array_map(function (ActionInterface $action) {
            return preg_replace('@^\./@', '', $action->getDestination());
        }, $filtered);
    }
}
