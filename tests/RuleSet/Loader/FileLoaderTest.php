<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader;

use SensioLabs\DeprecationDetector\RuleSet\Loader\FileLoader;

class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $loader = new FileLoader();

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\RuleSet\Loader\FileLoader', $loader);
    }

    public function testLoadingNotExistingFileThrowsAnException()
    {
        $loader = new FileLoader();

        $this->setExpectedException(
            'SensioLabs\DeprecationDetector\RuleSet\Loader\CouldNotLoadRuleSetException',
            '<error>Ruleset "no_such.file" does not exist, aborting.</error>'
        );
        $loader->loadRuleSet('no_such.file');
    }

    public function testLoadRuleSetThrowsExceptionIfCachedIsNotAnInstanceOfRuleset()
    {
        //@TODO: file_get_contents is untestable
        $this->markTestSkipped();
    }
}
