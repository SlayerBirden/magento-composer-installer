<?php

namespace MagentoHackathon\Composer\Magento\Factory\Directives;

use Composer\Package\PackageInterface;
use MagentoHackathon\Composer\Magento\Directives\Action\Add;
use MagentoHackathon\Composer\Magento\Directives\Action\Remove;
use MagentoHackathon\Composer\Magento\Directives\Action\Update;
use MagentoHackathon\Composer\Magento\Directives\Bag;
use MagentoHackathon\Composer\Magento\Parser\CsvMapParser;

class ActionBagFactory
{
    /**
     * @param PackageInterface $package
     * @param $sourceDir
     * @return Bag
     */
    public function make(PackageInterface $package, $sourceDir)
    {
        $file = sprintf('%s/directives.csv', $sourceDir);
        if (!file_exists($file)) {
            return new Bag();
        }
        $parser = new CsvMapParser($file);
        return $this->parseMappings($parser->getMappings());
    }

    /**
     * @param $mapping
     * @return Bag
     * @throws \ErrorException
     */
    public function parseMappings($mapping)
    {
        $bag = new Bag();
        $i = 0;
        foreach ($mapping as $row) {
            if (!isset($row[0]) || !isset($row[1]) || !isset($row[2])) {
                throw new \ErrorException(sprintf("Invalid row in directives.csv #%d: %s", $i + 1, json_encode($row)));
            }
            list($type, $source, $destination) = $row;
            switch ($type) {
                case 'add':
                case 'create':
                    $action = new Add($source, $destination);
                    break;
                case 'update':
                    $action = new Update($source, $destination);
                    break;
                case 'delete':
                case 'remove':
                    $action = new Remove($source, $destination);
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf("Unrecognizable action type on row #%d: %s", $i, $type));
            }
            $bag->add($action);
            $i++;
        }
        return $bag;
    }
}
