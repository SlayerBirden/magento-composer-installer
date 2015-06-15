<?php

namespace MagentoHackathon\Composer\Magento;

/**
 * Class InstalledPackageDumper
 * @package MagentoHackathon\Composer\Magento
 */
class InstalledPackageDumper
{
    /**
     * @param InstalledPackage $installedPackage
     * @return array
     */
    public function dump(InstalledPackage $installedPackage)
    {
        return array(
            'packageName'       => $installedPackage->getName(),
            'version'           => $installedPackage->getVersion(),
            'installedFiles'    => $installedPackage->getInstalledFiles(),
            'ref'               => $installedPackage->getRef(),
            'currentDirectives' => $installedPackage->getCurrentDirectives(),
        );
    }

    /**
     * @param array $data
     * @return InstalledPackage
     */
    public function restore(array $data)
    {
        $package = new InstalledPackage($data['packageName'], $data['version'], $data['installedFiles']);
        if (isset($data['currentDirectives'])) {
            $package->setCurrentDirectives($data['currentDirectives']);
        }
        if (isset($data['ref'])) {
            $package->setRef($data['ref']);
        }
        return $package;
    }
}
