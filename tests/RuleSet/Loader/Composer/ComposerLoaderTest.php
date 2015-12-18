<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader\Composer;

use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\ComposerLoader;

class ComposerLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $traverser = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser')->reveal();
        $cache = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Cache')->reveal();
        $factory = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\ComposerFactory')->reveal();

        $loader = new ComposerLoader($traverser, $cache, $factory);
    }
}