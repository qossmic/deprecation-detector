<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader;

use SensioLabs\DeprecationDetector\RuleSet\Loader\DirectoryLoader;

class DirectoryLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $traverser = $this->prophesize('\SensioLabs\DeprecationDetector\RuleSet\Traverser');
        $cache = $this->prophesize('\SensioLabs\DeprecationDetector\RuleSet\Cache');

        $loader = new DirectoryLoader($traverser->reveal(), $cache->reveal());

        $this->assertInstanceOf('\SensioLabs\DeprecationDetector\RuleSet\Loader\DirectoryLoader', $loader);
    }
}
