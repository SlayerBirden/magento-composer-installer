<?php

namespace MagentoHackathon\Composer\Magento;

use Composer\Package\PackageInterface;
use MagentoHackathon\Composer\Magento\Directives\ArrayDumper;
use MagentoHackathon\Composer\Magento\Event\EventManager;
use MagentoHackathon\Composer\Magento\Event\PackageDeployEvent;
use MagentoHackathon\Composer\Magento\Factory\Directives\ActionBagFactory;
use MagentoHackathon\Composer\Magento\Factory\EntryFactory;
use MagentoHackathon\Composer\Magento\Repository\InstalledPackageRepositoryInterface;
use MagentoHackathon\Composer\Magento\UnInstallStrategy\UnInstallStrategyInterface;

/**
 * Class ModuleManager
 * @package MagentoHackathon\Composer\Magento
 * @author  Aydin Hassan <aydin@hotmail.co.uk>
 */
class ModuleManager
{
    /**
     * @var InstalledPackageRepositoryInterface
     */
    protected $installedPackageRepository;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var ProjectConfig
     */
    protected $config;

    /**
     * @var UnInstallStrategyInterface
     */
    protected $unInstallStrategy;
    /**
     * @var EntryFactory
     */
    private $entryFactory;

    /**
     * @param InstalledPackageRepositoryInterface $installedRepository
     * @param EventManager $eventManager
     * @param ProjectConfig $config
     * @param UnInstallStrategyInterface $unInstallStrategy
     * @param EntryFactory $entryFactory
     */
    public function __construct(
        InstalledPackageRepositoryInterface $installedRepository,
        EventManager $eventManager,
        ProjectConfig $config,
        UnInstallStrategyInterface $unInstallStrategy,
        EntryFactory $entryFactory
    ) {
        $this->installedPackageRepository = $installedRepository;
        $this->eventManager = $eventManager;
        $this->config = $config;
        $this->unInstallStrategy = $unInstallStrategy;
        $this->entryFactory = $entryFactory;
    }

    /**
     * @param $needle
     * @param array|InstalledPackage[] $haystack
     * @return bool|\MagentoHackathon\Composer\Magento\InstalledPackage
     */
    protected function getPackageByName($needle, array $haystack)
    {
        foreach ($haystack as $package) {
            if ($package instanceof InstalledPackage) {
                if ($package->getName() == $needle) {
                    return $package;
                }
            }
        }
        return false;
    }

    /**
     * @param array $currentComposerInstalledPackages
     * @return array
     */
    public function updateInstalledPackages(array $currentComposerInstalledPackages)
    {
        $magentoInstalledPackages = $this->installedPackageRepository->findAll();
        $packagesToRemove = $this->getRemoves(
            $currentComposerInstalledPackages,
            $magentoInstalledPackages
        );

        $packagesToInstall  = $this->getInstalls($currentComposerInstalledPackages);

        $this->doRemoves($packagesToRemove);
        //$this->doInstalls($packagesToInstall);

        foreach ($packagesToInstall as $install) {
            $deployEntry = $this->entryFactory->make($install, $this->getPackageSourceDirectory($install));
            $deployEntry->setPackageName($install->getPrettyName());
            $this->eventManager->dispatch(
                new PackageDeployEvent('pre-package-deploy', $deployEntry)
            );
            $initialBag = clone $deployEntry->getDeployStrategy()->getActionBag();
            $factory = new ActionBagFactory();
            $swapBag = false;
            if (($installed = $this->getPackageByName($install->getName(), $magentoInstalledPackages)) && $initialBag) {
                $deployEntry->getDeployStrategy()
                    ->setActionBag(
                        $deployEntry->getDeployStrategy()
                        ->getActionBag()
                        ->diff($factory->parseMappings($installed->getCurrentDirectives()))
                    );
                $swapBag = true;
            }

            $files = $deployEntry->getDeployStrategy()->deploy()->getDeployedFiles();

            if ($swapBag) {
                // set full bag for further hooks
                $deployEntry->getDeployStrategy()->setActionBag($initialBag);
            }

            $this->eventManager->dispatch(
                new PackageDeployEvent('post-package-deploy', $deployEntry)
            );
            $arrayDumper = new ArrayDumper();
            $this->installedPackageRepository->add(new InstalledPackage(
                $install->getName(),
                $install->getVersion(),
                $files,
                ($install->getInstallationSource() == 'source' ? $install->getSourceReference() : $install->getDistReference()),
                $arrayDumper->dump($initialBag)
            ));
        }

        return array(
            $packagesToRemove,
            $packagesToInstall
        );
    }

    /**
     * @param InstalledPackage[] $packagesToRemove
     */
    public function doRemoves(array $packagesToRemove)
    {
        $magentoRootDir = $this->config->getMagentoRootDir();
        $addBasePath = function($path) use ($magentoRootDir) {
            return $magentoRootDir.$path;
        };
        foreach ($packagesToRemove as $remove) {
            //$this->eventManager->dispatch(new PackageUnInstallEvent('pre-package-uninstall', $remove));
            // do not remove code if we have diff
            if ($this->config->getModuleSpecificDeployStrategy($remove->getName()) != 'diff') {
                $this->unInstallStrategy->unInstall(array_map($addBasePath, $remove->getInstalledFiles()));
            }
            //$this->eventManager->dispatch(new PackageUnInstallEvent('post-package-uninstall', $remove));
            $this->installedPackageRepository->remove($remove);
        }
    }

    /**
     * @param PackageInterface[] $currentComposerInstalledPackages
     * @param InstalledPackage[] $magentoInstalledPackages
     * @return InstalledPackage[]
     */
    public function getRemoves(array $currentComposerInstalledPackages, array $magentoInstalledPackages)
    {
        //make the package names as the array keys
        $currentComposerInstalledPackages = array_combine(
            array_map(
                function (PackageInterface $package) {
                    return $package->getPrettyName();
                },
                $currentComposerInstalledPackages
            ),
            $currentComposerInstalledPackages
        );

        return array_filter(
            $magentoInstalledPackages,
            function (InstalledPackage $package) use ($currentComposerInstalledPackages) {
                if (!isset($currentComposerInstalledPackages[$package->getName()])) {
                    return true;
                }
                /** @var PackageInterface $composerPackage */
                $composerPackage = $currentComposerInstalledPackages[$package->getName()];
                return $package->getUniqueRefName() !== $this->getPackageUniqueRefName($composerPackage);
            }
        );
    }

    /**
     * @param PackageInterface $package
     * @return string
     */
    protected function getPackageUniqueRefName(PackageInterface $package)
    {
        return sprintf('%s-%s',
            $package->getUniqueName(),
            ($package->getInstallationSource() == 'source' ?
                $package->getSourceReference() :
                $package->getDistReference())
        );
    }

    /**
     * @param PackageInterface[] $currentComposerInstalledPackages
     * @return PackageInterface[]
     */
    public function getInstalls(array $currentComposerInstalledPackages)
    {
        $repo = $this->installedPackageRepository;
        $packages = array_filter($currentComposerInstalledPackages, function(PackageInterface $package) use ($repo) {
            return !$repo->has($package->getName(),
                $package->getVersion(),
                ($package->getInstallationSource() == 'source' ?
                $package->getSourceReference() :
                $package->getDistReference())
            );
        });
        
        $config = $this->config;
        usort($packages, function(PackageInterface $aObject, PackageInterface $bObject) use ($config) {
            $a = $config->getModuleSpecificSortValue($aObject);
            $b = $config->getModuleSpecificSortValue($bObject);
            if ($a == $b) {
                return strcmp($aObject->getName(), $bObject->getName());
                /**
                 * still changes sort order and breaks a test, so for now strcmp as workaround
                 * to keep the test working.
                 */
                // return 0;
            }
            return ($a < $b) ? -1 : 1;
        });
        
        return $packages;
    }

    /**
     * @param PackageInterface $package
     * @return string
     */
    private function getPackageSourceDirectory(PackageInterface $package)
    {
        $path = sprintf("%s/%s", $this->config->getVendorDir(), $package->getPrettyName());
        $targetDir = $package->getTargetDir();

        if ($targetDir) {
            $path = sprintf("%s/%s", $path, $targetDir);
        }

        $path = realpath($path);
        return $path;
    }
}
