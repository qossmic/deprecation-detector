<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet;

use SensioLabs\DeprecationDetector\RuleSet\Cache;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use Symfony\Component\Filesystem\Filesystem;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $filesystem = $this->prophesize(Filesystem::class);
        $cache = new Cache($filesystem->reveal());

        $this->assertInstanceOf(Cache::class, $cache);
    }

    public function testIsEnabledAndDisable()
    {
        $filesystem = $this->prophesize(Filesystem::class);
        $cache = new Cache($filesystem->reveal());

        $this->assertTrue($cache->isEnabled());
        $cache->disable();
        $this->assertFalse($cache->isEnabled());
    }

    public function testHasKey()
    {
        $cachedir = '.rule-set';
        $key = 'some-rume-set';

        $filesystem = $this->prophesize(Filesystem::class);
        $filesystem->exists($cachedir.'/'.$key)->willReturn(true)->shouldBeCalled();

        $cache = new Cache($filesystem->reveal(), true, $cachedir);
        $this->assertTrue($cache->has($key));
        $cache->disable();
        $this->assertFalse($cache->has('someOtherKey'));
    }

    public function testSaveCacheIfEnabled()
    {
        $ruleset = $this->prophesize(RuleSet::class)->reveal();
        $filesystem = $this->prophesize(Filesystem::class);
        $filesystem->dumpFile('.rules/id', serialize($ruleset))->shouldBeCalled();

        $cache = new Cache($filesystem->reveal());
        $cache->cacheRuleSet('id', $ruleset);
    }

    public function testDoesNotSaveCacheIfDisabled()
    {
        $ruleset = $this->prophesize(RuleSet::class)->reveal();
        $filesystem = $this->prophesize(Filesystem::class);
        $filesystem->dumpFile('.rules/id', serialize($ruleset))->shouldNotBeCalled();

        $cache = new Cache($filesystem->reveal(), false);
        $cache->cacheRuleSet('id', $ruleset);
    }

    public function testGetCachedReturnsNullIfDisabled()
    {
        $filesystem = $this->prophesize(Filesystem::class);

        $cache = new Cache($filesystem->reveal(), false);
        $this->assertNull($cache->getCachedRuleSet('id'));
    }

    public function testGetCachedReturnsNullIfCacheIsNotExisting()
    {
        $filesystem = $this->prophesize(Filesystem::class);
        $filesystem->exists('.rules/id')->willReturn(false);

        $cache = new Cache($filesystem->reveal());
        $this->assertNull($cache->getCachedRuleSet('id'));
    }

    public function testGetCached()
    {
        //@TODO: file_get_contents is untestable
        $this->markTestSkipped();
    }
}
