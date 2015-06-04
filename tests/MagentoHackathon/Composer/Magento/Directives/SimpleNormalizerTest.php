<?php

namespace MagentoHackathon\Composer\Magento\Directives;

use MagentoHackathon\Composer\Magento\Directives\Action\Add;
use MagentoHackathon\Composer\Magento\Directives\Action\Remove;
use MagentoHackathon\Composer\Magento\Directives\Action\Update;

class SimpleNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalizeExcluded()
    {
        $normalizer = new SimpleNormalizer();
        $bag = new Bag();
        $bag->add(new Add('path1', 'path1'));
        $bag->add(new Add('path2', 'path2'));
        $bag->add(new Add('path3', 'path3'));
        $bag->add(new Remove('path3', 'path3'));
        $this->assertEquals([
            new Add('path1', 'path1'),
            new Add('path2', 'path2'),
        ], $normalizer->normalize($bag));
    }

    public function testNormalizeNonExisting()
    {
        $normalizer = new SimpleNormalizer();
        $bag = new Bag();
        $bag->add(new Add('path1', 'path1'));
        $bag->add(new Add('path2', 'path2'));
        $bag->add(new Update('path3', 'path3'));
        $bag->add(new Remove('path3', 'path3'));
        $this->assertEquals([
            new Add('path1', 'path1'),
            new Add('path2', 'path2'),
            new Remove('path3', 'path3'),
        ], $normalizer->normalize($bag));
    }

    public function testNormalizeBoth()
    {
        $normalizer = new SimpleNormalizer();
        $bag = new Bag();
        $bag->add(new Add('path1', 'path1'));
        $bag->add(new Add('path2', 'path2'));
        $bag->add(new Add('path3', 'path3'));
        $bag->add(new Update('path3', 'path3'));
        $bag->add(new Remove('path3', 'path3'));
        $this->assertEquals([
            new Add('path1', 'path1'),
            new Add('path2', 'path2'),
        ], $normalizer->normalize($bag));
    }

    public function testAddUpdate()
    {
        $normalizer = new SimpleNormalizer();
        $bag = new Bag();
        $bag->add(new Add('path1', 'path1'));
        $bag->add(new Add('path2', 'path2'));
        $bag->add(new Add('path3', 'path3'));
        $bag->add(new Update('path3', 'path3'));
        $this->assertEquals([
            new Add('path1', 'path1'),
            new Add('path2', 'path2'),
            new Add('path3', 'path3'),
        ], $normalizer->normalize($bag));
    }
}
