<?php

namespace MagentoHackathon\Composer\Magento\Factory;

use Composer\Package\PackageInterface;
use MagentoHackathon\Composer\Magento\Deploystrategy\Core;
use MagentoHackathon\Composer\Magento\Directives\Bag;
use MagentoHackathon\Composer\Magento\Event\EventManager;
use MagentoHackathon\Composer\Magento\Factory\Directives\ActionBagFactory;
use MagentoHackathon\Composer\Magento\Plugin;
use MagentoHackathon\Composer\Magento\ProjectConfig;
use MagentoHackathon\Composer\Magento\Deploystrategy\DeploystrategyAbstract;

/**
 * Class DeploystrategyFactory
 * @package MagentoHackathon\Composer\Magento\Deploystrategy
 */
class DeploystrategyFactory
{
    /**
     * @var ProjectConfig
     */
    protected $config;

    /**
     * @var array
     */
    protected static $strategies = array(
        'copy'      => '\MagentoHackathon\Composer\Magento\Deploystrategy\Copy',
        'symlink'   => '\MagentoHackathon\Composer\Magento\Deploystrategy\Symlink',
        'link'      => '\MagentoHackathon\Composer\Magento\Deploystrategy\Link',
        'none'      => '\MagentoHackathon\Composer\Magento\Deploystrategy\None',
        'diff'      => '\MagentoHackathon\Composer\Magento\Deploystrategy\Diff',
    );
    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @param ProjectConfig $config
     * @param EventManager $eventManager
     */
    public function __construct(ProjectConfig $config, EventManager $eventManager)
    {
        $this->config = $config;
        $this->eventManager = $eventManager;
    }

    /**
     * @param PackageInterface $package
     * @param string $packageSourcePath
     * @return DeploystrategyAbstract
     */
    public function make(PackageInterface $package, $packageSourcePath)
    {
        $strategyName = $this->config->getDeployStrategy();
        if ($this->config->hasDeployStrategyOverwrite()) {
            $moduleSpecificDeployStrategies = $this->config->getDeployStrategyOverwrite();

            if (isset($moduleSpecificDeployStrategies[$package->getName()])) {
                $strategyName = $moduleSpecificDeployStrategies[$package->getName()];
            }
        }

        if (!isset(static::$strategies[$strategyName])) {
            $className = static::$strategies['symlink'];
        } else {
            $className = static::$strategies[$strategyName];
        }

        /** @var DeploystrategyAbstract $strategy */
        $strategy = new $className($packageSourcePath, realpath($this->config->getMagentoRootDir()));
        $strategy->setIgnoredMappings($this->config->getModuleSpecificDeployIgnores($package->getName()));
        $strategy->setIsForced($this->config->getMagentoForceByPackageName($package->getName()));
        $actionFactory = new ActionBagFactory();
        $bag = $actionFactory->make($package, $packageSourcePath);
        $strategy->setActionBag($bag);
        $strategy->setEventManager($this->eventManager);
        return $strategy;
    }
}
