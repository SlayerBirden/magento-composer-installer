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
     * @param array $currentComposerInstalledPackages
     * @return array
     */
    public function updateInstalledPackages(array $currentComposerInstalledPackages)
    {
        $packagesToRemove = $this->getRemoves(
            $currentComposerInstalledPackages,
            $this->installedPackageRepository->findAll()
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
            $actualBag = $deployEntry->getDeployStrategy()->getActionBag();
            $factory = new ActionBagFactory();
            if (isset($currentComposerInstalledPackages[$install->getName()]) && $actualBag) {
                $deployEntry->getDeployStrategy()
                    ->setActionBag($actualBag->diff($factory->parseMappings($currentComposerInstalledPackages[$install->getName()])));
            }

            $files = $deployEntry->getDeployStrategy()->deploy()->getDeployedFiles();

            $this->eventManager->dispatch(
                new PackageDeployEvent('post-package-deploy', $deployEntry)
            );
            $arrayDumper = new ArrayDumper();
            $this->installedPackageRepository->add(new InstalledPackage(
                $install->getName(),
                $install->getVersion(),
                $files,
                $arrayDumper->dump($actualBag)
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

                $composerPackage = $currentComposerInstalledPackages[$package->getName()];
                return $package->getUniqueName() !== $composerPackage->getUniqueName();
            }
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
            return !$repo->has($package->getName(), $package->getVersion());
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
