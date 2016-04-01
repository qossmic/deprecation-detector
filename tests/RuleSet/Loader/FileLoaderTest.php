<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader;

use SensioLabs\DeprecationDetector\RuleSet\Loader\CouldNotLoadRuleSetException;
use SensioLabs\DeprecationDetector\RuleSet\Loader\FileLoader;

class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $loader = new FileLoader();

        $this->assertInstanceOf(FileLoader::class, $loader);
    }

    public function testLoadingNotExistingFileThrowsAnException()
    {
        $loader = new FileLoader();

        $this->setExpectedException(
            CouldNotLoadRuleSetException::class,
            'Ruleset "no_such.file" does not exist, aborting.'
        );
        $loader->loadRuleSet('no_such.file');
    }

    public function testLoadRuleSetThrowsExceptionIfCachedIsNotAnInstanceOfRuleset()
    {
        //@TODO: file_get_contents is untestable
        $this->markTestSkipped();
    }
}
