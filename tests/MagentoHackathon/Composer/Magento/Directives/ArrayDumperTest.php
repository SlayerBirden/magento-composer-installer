<?php

namespace MagentoHackathon\Composer\Magento\Directives;

use MagentoHackathon\Composer\Magento\Directives\Action\Add;

class ArrayDumperTest extends \PHPUnit_Framework_TestCase
{
    public function testDump()
    {
        $dumper = new ArrayDumper();
        $bag = new Bag();
        $bag->add(new Add('path1', 'path1'));
        $bag->add(new Add('path2', 'path2'));
        $this->assertEquals([
            ['add', 'path1', 'path1'],
            ['add', 'path2', 'path2'],
        ], $dumper->dump($bag));
    }
}
