<?php

namespace MagentoHackathon\Composer\Magento;

use Composer\Package\Package;
use Composer\Package\PackageInterface;

class ProjectConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @param PackageInterface $package
     * @param $expected
     * @dataProvider pluginDataProvider
     */
    public function getModuleSpecificSortValue(PackageInterface $package, $expected)
    {
        $config = new ProjectConfig([
            ProjectConfig::MAGENTO_DEPLOY_STRATEGY_KEY => 'copy',
            ProjectConfig::MAGENTO_DEPLOY_STRATEGY_OVERWRITE_KEY => [
                'test/test2' => 'symlink',
            ],
        ], []);
        $this->assertEquals($expected, $config->getModuleSpecificSortValue($package));
    }

    /**
     * @return array
     */
    public function pluginDataProvider()
    {
        $packageCore = new Package('test/test', '1.0', '1.0');
        $packageCore->setType('magento-core');
        return [
            [new Package('test/test', '1.0', '1.0'), 101],
            [new Package('test/test2', '1.0', '1.0'), 100],
            [$packageCore, PHP_INT_MAX],
        ];
    }
}
