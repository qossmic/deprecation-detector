<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader;

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

    public function testLoadRuleSetThrowsExceptionIfCachedIsNotAnInstanceOfRuleset()
    {
        //@TODO: file_get_contents is untestable
        $this->markTestSkipped();
    }

    protected function getInstance()
    {
        $dispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcher');
        return new \SensioLabs\DeprecationDetector\RuleSet\Loader\FileLoader($dispatcher->reveal());
    }
}
