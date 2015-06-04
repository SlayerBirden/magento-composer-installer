<?php

namespace MagentoHackathon\Composer\Magento\Factory\Directives;
use Composer\Package\Package;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class ActionBagFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    protected $root;

    public function setUp()
    {
        $this->root = vfsStream::setup('root');
    }

    public function testCreateBag()
    {
        $content = <<<'CSV'
add,./file1,./file1
add,./file2,./file2
update,./file2,./file2
CSV;
        vfsStream::newFile('directives.csv')->at($this->root)->setContent($content);
        $factory = new ActionBagFactory();
        $bag = $factory->make(new Package('test/test', 1, 1), vfsStream::url('root'));
        $this->assertCount(3, $bag);
    }
}
