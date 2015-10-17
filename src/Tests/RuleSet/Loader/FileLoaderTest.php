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

    public function testLoadingNotExistingFileThrowsAnException()
    {
        //@TODO: is_file is untestable
        $this->markTestSkipped();
    }

    public function testLoadRuleSetThrowsExceptionIfCachedIsNotAnInstanceOfRuleset()
    {
        //@TODO: file_get_contents is untestable
        $this->markTestSkipped();
    }
}
