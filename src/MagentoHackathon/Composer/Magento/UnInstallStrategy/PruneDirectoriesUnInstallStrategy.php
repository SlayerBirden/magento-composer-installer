<?php

namespace MagentoHackathon\Composer\Magento\UnInstallStrategy;

use Composer\Util\Filesystem;

class PruneDirectoriesUnInstallStrategy implements UnInstallStrategyInterface
{

    /**
     * @var FileSystem
     */
    protected $fileSystem;

    /**
     * @var array
     */
    protected $directories = [];

    /**
     * @param FileSystem $fileSystem
     */
    public function __construct(FileSystem $fileSystem)
    {

        $this->fileSystem = $fileSystem;
    }

    /**
     * UnInstall the extension given the list of install files
     *
     * @param array $files
     */
    public function unInstall(array $files)
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                array_merge($this->directories, $this->getDirectories($file));
                $this->fileSystem->unlink($file);
            }
        }
        // prune directories
        foreach ($this->directories as $dir) {
            if ($this->fileSystem->isDirEmpty($dir)) {
                $this->fileSystem->removeDirectory($dir);
            }
        }
    }

    /**
     * @param string $file
     * @return array
     */
    public function getDirectories($file)
    {
        $dirs = [];
        while (dirname($file) != '.') {
            $dirs[] = $file = dirname($file);
        }
        return $dirs;
    }
}
