<?php

namespace MagentoHackathon\Composer\Magento\Parser;

/**
 * Class MapParser
 * @package MagentoHackathon\Composer\Magento\Parser
 */
class CsvMapParser extends MapParser
{
    /**
     * @var \SplFileObject The map.csv file
     */
    protected $file = null;

    /**
     * @param string $mapFile
     */
    public function __construct($mapFile)
    {
        $this->file = new \SplFileObject($mapFile);
    }

    /**
     * @throws \ErrorException
     * @return array
     */
    public function getMappings()
    {
        if (!$this->file->isReadable()) {
            throw new \ErrorException(sprintf('Mapping file "%s" not readable', $this->file->getPathname()));
        }

        $map = $this->parseMappings();
        return $map;
    }

    /**
     * @return array
     */
    protected function parseMappings()
    {
        $map = array();
        while (!$this->file->eof()) {
            $row = $this->file->fgetcsv();
            $row = array_filter(array_map('trim', $row));
            if ($row) {
                $map[] = $row;
            }
        }

        return $map;
    }
}
