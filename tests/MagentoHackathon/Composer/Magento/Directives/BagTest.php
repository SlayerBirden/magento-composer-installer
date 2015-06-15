<?php

namespace MagentoHackathon\Composer\Magento\Directives;

use MagentoHackathon\Composer\Magento\Directives\Action\Add;

class BagTest extends \PHPUnit_Framework_TestCase
{
    public function testDiff()
    {
        $bag = new Bag();
        $bag->add(new Add('path1', 'path1'));
        $bag->add(new Add('path2', 'path2'));
        $bag->add(new Add('path3', 'path3'));
        $bag2 = new Bag();
        $bag2->add(new Add('path1', 'path1'));
        $bag2->add(new Add('path2', 'path2'));

        $expected = new Bag();
        $expected->add(new Add('path3', 'path3'));
        $this->assertEquals($expected, $bag->diff($bag2));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Can not diff bags, different actions on
     */
    public function testDiffException()
    {
        $bag = new Bag();
        $bag->add(new Add('path1', 'path1'));
        $bag->add(new Add('path2', 'path2'));
        $bag->add(new Add('path3', 'path3'));
        $bag2 = new Bag();
        $bag2->add(new Add('path1', 'path1'));
        $bag2->add(new Add('path4', 'path4'));

        $bag->diff($bag2);
    }
}
