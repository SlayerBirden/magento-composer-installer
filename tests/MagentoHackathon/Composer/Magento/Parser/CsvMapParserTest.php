<?php

namespace MagentoHackathon\Composer\Magento\Parser;

use org\bovigo\vfs\vfsStream;

class CsvMapParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $fixtureDir;

    public function setUp()
    {
        $root = vfsStream::setup('root');
        vfsStream::copyFromFileSystem(realpath(__DIR__ . '/../../../../res/fixtures'), $root);
    }

    public function testGetMappings()
    {
        $parser = new CsvMapParser(vfsStream::url('root/map.csv'));

        $expected = array (
            array(
                './app/code/community/Some/Module/Block/Block.php',
                './app/code/community/Some/Module/Block/Block.php'
            ),
            array(
                './app/code/community/Some/Module/Helper/Data.php',
                './app/code/community/Some/Module/Helper/Data.php'
            )
        );

        $this->assertSame($expected, $parser->getMappings());
    }

    /**
     * @expectedException \ErrorException
     * @expectedExceptionMessage Mapping file "vfs://root/map.csv" not readable
     */
    public function testExceptionIsThrowIfFileNotReadable()
    {
        $parser = new CsvMapParser(vfsStream::url('root/map.csv'));
        chmod(vfsStream::url('root/map.csv'), 0000);
        $parser->getMappings();
    }
}
