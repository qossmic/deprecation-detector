<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader;

class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $dispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcher');
        $loader = new \SensioLabs\DeprecationDetector\RuleSet\Loader\FileLoader($dispatcher->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\RuleSet\Loader\FileLoader', $loader);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Rule set file "no_such.file" does not exist.
     */
    public function testLoadingNotExistingFileThrowsAnException()
    {
        $dispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcher');
        $loader = new \SensioLabs\DeprecationDetector\RuleSet\Loader\FileLoader($dispatcher->reveal());

        $loader->loadRuleSet('no_such.file');
    }

    public function testLoadRuleSetThrowsExceptionIfCachedIsNotAnInstanceOfRuleset()
    {
        //@TODO: file_get_contents is untestable
        $this->markTestSkipped();
    }
}
