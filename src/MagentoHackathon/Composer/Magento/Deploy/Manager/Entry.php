<?php
/**
 *
 *
 *
 *
 */

namespace MagentoHackathon\Composer\Magento\Deploy\Manager;

class Entry
{
    /** @var string */
    protected $packageName;
    /** @var string */
    protected $packageType;

    /**
     * @var \MagentoHackathon\Composer\Magento\Deploystrategy\DeploystrategyAbstract
     */
    protected $deployStrategy;

    /**
     * @param mixed $packageName
     */
    public function setPackageName($packageName)
    {
        $this->packageName = $packageName;
    }

    /**
     * @return mixed
     */
    public function getPackageName()
    {
        return $this->packageName;
    }

    /**
     * @return string
     */
    public function getPackageType()
    {
        return $this->packageType;
    }

    /**
     * @param string $packageType
     */
    public function setPackageType($packageType)
    {
        $this->packageType = $packageType;
    }

    /**
     * @param \MagentoHackathon\Composer\Magento\Deploystrategy\DeploystrategyAbstract $deployStrategy
     */
    public function setDeployStrategy($deployStrategy)
    {
        $this->deployStrategy = $deployStrategy;
    }

    /**
     * @return \MagentoHackathon\Composer\Magento\Deploystrategy\DeploystrategyAbstract
     */
    public function getDeployStrategy()
    {
        return $this->deployStrategy;
    }
}
