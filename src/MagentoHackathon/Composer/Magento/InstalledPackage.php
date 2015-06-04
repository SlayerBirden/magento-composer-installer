<?php

namespace MagentoHackathon\Composer\Magento;

/**
 * Class InstalledPackage
 * @package MagentoHackathon\Composer\Magento
 * @author Aydin Hassan <aydin@wearejh.com>
 */
class InstalledPackage
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var array
     */
    protected $installedFiles;

    /**
     * @var array
     */
    protected $currentDirectives;

    /**
     * @param array $currentDirectives
     */
    public function setCurrentDirectives($currentDirectives)
    {
        $this->currentDirectives = $currentDirectives;
    }

    /**
     * @param string $name
     * @param string $version
     * @param array $files
     * @param array $currentDirectives
     */
    public function __construct($name, $version, array $files, array $currentDirectives = array())
    {
        $this->name = $name;
        $this->installedFiles = $files;
        $this->version = $version;
        $this->currentDirectives = $currentDirectives;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getUniqueName()
    {
        return sprintf('%s-%s', $this->getName(), $this->getVersion());
    }

    /**
     * @return array
     */
    public function getInstalledFiles()
    {
        return $this->installedFiles;
    }

    /**
     * @return array
     */
    public function getCurrentDirectives()
    {
        return $this->currentDirectives;
    }
}
