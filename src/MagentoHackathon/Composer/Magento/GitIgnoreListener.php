<?php

namespace MagentoHackathon\Composer\Magento;

use MagentoHackathon\Composer\Magento\Event\PackageDeployEvent;
use MagentoHackathon\Composer\Magento\Event\PackageUnInstallEvent;

/**
 * Class GitIgnoreListener
 * @package MagentoHackathon\Composer\Magento
 * @author  Aydin Hassan <aydin@hotmail.co.uk>
 */
class GitIgnoreListener
{

    /**
     * @var GitIgnore
     */
    protected $gitIgnore;

    /**
     * @param GitIgnore $gitIgnore
     */
    public function __construct(GitIgnore $gitIgnore)
    {
        $this->gitIgnore = $gitIgnore;
    }

    /**
     * Add any files which were deployed to the .gitignore
     * Remove any files which were removed to the .gitignore
     *
     * @param PackageDeployEvent $packageDeployEvent
     */
    public function __invoke(PackageDeployEvent $packageDeployEvent)
    {
        $this->gitIgnore->addMultipleEntries(
            $packageDeployEvent->getDeployEntry()->getDeployStrategy()->getDeployedFiles()
        );

        $this->gitIgnore->removeMultipleEntries(
            $packageDeployEvent->getDeployEntry()->getDeployStrategy()->getRemovedFiles()
        );

        $this->gitIgnore->write();
    }

    /**
     * remove package files from gitignore
     * @param PackageUnInstallEvent $packageEvent
     */
    public function packageRemoved(PackageUnInstallEvent $packageEvent)
    {
        $this->gitIgnore->removeMultipleEntries(
            $packageEvent->getPackage()->getInstalledFiles()
        );

        $this->gitIgnore->write();
    }
}
