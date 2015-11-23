<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader;

use org\bovigo\vfs\vfsStream;

class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $this->assertInstanceOf('SensioLabs\DeprecationDetector\RuleSet\Loader\FileLoader', $this->getInstance());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Rule set file "no_such.file" does not exist.
     */
    public function testLoadingNotExistingFileThrowsAnException()
    {
        $this->getInstance()->loadRuleSet('no_such.file');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Rule set file is not valid.
     */
    public function testLoadRuleSetThrowsExceptionIfCachedIsNotAnInstanceOfRuleset()
    {
        $dummy = 'This is not a RuleSet';

        $root = vfsStream::setup();
        $virtualFile = vfsStream::newFile('dummy')
            ->withContent(serialize($dummy))
            ->at($root);

        $this->getInstance()->loadRuleSet($virtualFile->url());
    }

    protected function getInstance()
    {
        $dispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcher');
        return new \SensioLabs\DeprecationDetector\RuleSet\Loader\FileLoader($dispatcher->reveal());
    }
}
