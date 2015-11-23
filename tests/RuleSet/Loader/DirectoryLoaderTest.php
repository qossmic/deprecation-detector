<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader;

use Prophecy\Argument;
use SensioLabs\DeprecationDetector\RuleSet\Loader\DirectoryLoader;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;

class DirectoryLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $traverser = $this->prophesize('\SensioLabs\DeprecationDetector\RuleSet\Traverser');
        $cache = $this->prophesize('\SensioLabs\DeprecationDetector\RuleSet\Cache');

        $loader = new DirectoryLoader($traverser->reveal(), $cache->reveal());

        $this->assertInstanceOf('\SensioLabs\DeprecationDetector\RuleSet\Loader\DirectoryLoader', $loader);
    }

    public function testLoadRuleSetUncached()
    {
        $traverser = $this->prophesize('\SensioLabs\DeprecationDetector\RuleSet\Traverser');
        $traverser->traverse(Argument::any())
            ->willReturn(new RuleSet());

        $cache = $this->prophesize('\SensioLabs\DeprecationDetector\RuleSet\Cache');

        $loader = new DirectoryLoader($traverser->reveal(), $cache->reveal());

        $actualRuleSet = $loader->loadRuleSet('any');

        $this->assertInstanceOf('\SensioLabs\DeprecationDetector\RuleSet\RuleSet', $actualRuleSet);
    }

    public function testLoadRuleSetCached()
    {
        $traverser = $this->prophesize('\SensioLabs\DeprecationDetector\RuleSet\Traverser');

        $cache = $this->prophesize('\SensioLabs\DeprecationDetector\RuleSet\Cache');
        $cache->has(Argument::any())
            ->willReturn(true);
        $cache->getCachedRuleSet(Argument::any())
            ->willReturn(new RuleSet());

        $loader = new DirectoryLoader($traverser->reveal(), $cache->reveal());

        $actualRuleSet = $loader->loadRuleSet('any');

        $this->assertInstanceOf('\SensioLabs\DeprecationDetector\RuleSet\RuleSet', $actualRuleSet);
    }
}
