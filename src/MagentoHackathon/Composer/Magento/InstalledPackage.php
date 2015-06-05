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
     * @var string
     */
    protected $ref;

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
     * @param string $ref
     * @param array $currentDirectives
     */
    public function __construct($name, $version, array $files, $ref = '', array $currentDirectives = array())
    {
        $this->name = $name;
        $this->installedFiles = $files;
        $this->version = $version;
        $this->currentDirectives = $currentDirectives;
        $this->ref = $ref;
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
     * @return string
     */
    public function getUniqueRefName()
    {
        return sprintf('%s-%s-%s', $this->getName(), $this->getVersion(), $this->getRef());
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

    /**
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * @param string $ref
     */
    public function setRef($ref)
    {
        $this->ref = $ref;
    }
}
