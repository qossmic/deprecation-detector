<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader\Composer;

use org\bovigo\vfs\vfsStream;
use Prophecy\Argument;
use SensioLabs\DeprecationDetector\RuleSet\Cache;
use SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Composer;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\ComposerFactory;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\ComposerLoader;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Exception\ComposerFileDoesNotExistsException;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Package;
use SensioLabs\DeprecationDetector\RuleSet\Loader\CouldNotLoadRuleSetException;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;

class ComposerLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $traverser = $this->prophesize(DirectoryTraverser::class)->reveal();
        $cache = $this->prophesize(Cache::class)->reveal();
        $factory = $this->prophesize(ComposerFactory::class)->reveal();

        $loader = new ComposerLoader($traverser, $cache, $factory);
        $this->assertInstanceOf(ComposerLoader::class, $loader);
    }

    public function testLoadRuleSetThrowsCouldNotLoadRuleSetException()
    {
        $path = 'path/to/not/existing/composer.lock';
        $traverser = $this->prophesize(DirectoryTraverser::class)->reveal();
        $cache = $this->prophesize(Cache::class)->reveal();
        $factory = $this->prophesize(ComposerFactory::class);
        $factory->fromLock($path)->willThrow(new ComposerFileDoesNotExistsException($path));

        $this->setExpectedException(
            CouldNotLoadRuleSetException::class,
            'composer.lock file "path/to/not/existing/composer.lock" does not exist.'
        );
        $loader = new ComposerLoader($traverser, $cache, $factory->reveal());
        $loader->loadRuleSet('path/to/not/existing/composer.lock');
    }

    public function testLoadRuleSet()
    {
        $root = vfsStream::setup('root');
        $vendorDir = vfsStream::newDirectory('vendor');

        $vendor = vfsStream::newDirectory('avendor');
        $vendor->addChild(vfsStream::newDirectory('alib'));
        $vendorDir->addChild($vendor);

        $vendor = vfsStream::newDirectory('avendor');
        $vendor->addChild(vfsStream::newDirectory('anotherlib'));
        $vendorDir->addChild($vendor);

        $root->addChild($vendorDir);

        $aVendorALib = $this->prophesize(Package::class);
        $aVendorALib->generatePackageKey()->willReturn('vendor_alib_1.0.0');
        $aVendorALib->getPackagePath(Argument::any())->willReturn(vfsStream::url('root/vendor/avendor/alib'));
        $aVendorALibRuleSet = $this->prophesize(RuleSet::class);
        $aVendorALibRuleSet->classDeprecations()->willReturn([]);
        $aVendorALibRuleSet->interfaceDeprecations()->willReturn([]);
        $aVendorALibRuleSet->methodDeprecations()->willReturn([]);
        $aVendorALibRuleSet->functionDeprecations()->willReturn([]);
        $aVendorALibRuleSet = $aVendorALibRuleSet->reveal();

        $aVendorAnotherLib = $this->prophesize(Package::class);
        $aVendorAnotherLib->generatePackageKey()->willReturn('vendor_anotherlib_1.0.0');
        $aVendorAnotherLib->getPackagePath(Argument::any())->willReturn(vfsStream::url('root/vendor/avendor/anotherlib'));
        $aVendorAnotherLibRuleSet = $this->prophesize(RuleSet::class);
        $aVendorAnotherLibRuleSet->classDeprecations()->willReturn([]);
        $aVendorAnotherLibRuleSet->interfaceDeprecations()->willReturn([]);
        $aVendorAnotherLibRuleSet->methodDeprecations()->willReturn([]);
        $aVendorAnotherLibRuleSet->functionDeprecations()->willReturn([]);
        $aVendorAnotherLibRuleSet = $aVendorAnotherLibRuleSet->reveal();

        $anotherVendorALib = $this->prophesize(Package::class);
        $anotherVendorALib->generatePackageKey()->willReturn('anothervendor_alib_1.0.0');
        $anotherVendorALib->getPackagePath(Argument::any())->willReturn('not/existing/path/because/it/is/a/dev/dependency');

        $composer = $this->prophesize(Composer::class);
        $composer->getPackages()->willReturn([$aVendorALib, $aVendorAnotherLib, $anotherVendorALib]);

        $cache = $this->prophesize(Cache::class);
        $cache->has('vendor_alib_1.0.0')->willReturn(true);
        $cache->getCachedRuleSet('vendor_alib_1.0.0')->willReturn($aVendorALibRuleSet);
        $cache->has('vendor_anotherlib_1.0.0')->willReturn(false);
        $cache->has('anothervendor_alib_1.0.0')->willReturn(false);

        $traverser = $this->prophesize(DirectoryTraverser::class);
        $traverser->traverse(vfsStream::url('root/vendor/avendor/anotherlib'))->willReturn($aVendorAnotherLibRuleSet);
        $cache->cacheRuleSet('vendor_anotherlib_1.0.0', $aVendorAnotherLibRuleSet)->shouldBeCalled();

        $factory = $this->prophesize(ComposerFactory::class);
        $factory->fromLock(vfsStream::url('composer.lock'))->willReturn($composer);

        $loader = new ComposerLoader($traverser->reveal(), $cache->reveal(), $factory->reveal());
        $loader->loadRuleSet(vfsStream::url('composer.lock'));
    }
}
