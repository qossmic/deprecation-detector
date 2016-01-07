<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader\Composer;

use org\bovigo\vfs\vfsStream;
use Prophecy\Argument;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\ComposerLoader;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Exception\ComposerFileDoesNotExistsException;

class ComposerLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $traverser = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser')->reveal();
        $cache = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Cache')->reveal();
        $factory = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\ComposerFactory')->reveal();

        $loader = new ComposerLoader($traverser, $cache, $factory);
        $this->assertInstanceOf('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\ComposerLoader', $loader);
    }

    public function testLoadRuleSetThrowsCouldNotLoadRuleSetException()
    {
        $path = 'path/to/not/existing/composer.lock';
        $traverser = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser')->reveal();
        $cache = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Cache')->reveal();
        $factory = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\ComposerFactory');
        $factory->fromLock($path)->willThrow(new ComposerFileDoesNotExistsException($path));

        $this->setExpectedException(
            'SensioLabs\DeprecationDetector\RuleSet\Loader\CouldNotLoadRuleSetException',
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

        $aVendorALib = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Package');
        $aVendorALib->generatePackageKey()->willReturn('vendor_alib_1.0.0');
        $aVendorALib->getPackagePath(Argument::any())->willReturn(vfsStream::url('root/vendor/avendor/alib'));
        $aVendorALibRuleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $aVendorALibRuleSet->classDeprecations()->willReturn(array());
        $aVendorALibRuleSet->interfaceDeprecations()->willReturn(array());
        $aVendorALibRuleSet->methodDeprecations()->willReturn(array());
        $aVendorALibRuleSet->functionDeprecations()->willReturn(array());
        $aVendorALibRuleSet = $aVendorALibRuleSet->reveal();

        $aVendorAnotherLib = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Package');
        $aVendorAnotherLib->generatePackageKey()->willReturn('vendor_anotherlib_1.0.0');
        $aVendorAnotherLib->getPackagePath(Argument::any())->willReturn(vfsStream::url('root/vendor/avendor/anotherlib'));
        $aVendorAnotherLibRuleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $aVendorAnotherLibRuleSet->classDeprecations()->willReturn(array());
        $aVendorAnotherLibRuleSet->interfaceDeprecations()->willReturn(array());
        $aVendorAnotherLibRuleSet->methodDeprecations()->willReturn(array());
        $aVendorAnotherLibRuleSet->functionDeprecations()->willReturn(array());
        $aVendorAnotherLibRuleSet = $aVendorAnotherLibRuleSet->reveal();

        $anotherVendorALib = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Package');
        $anotherVendorALib->generatePackageKey()->willReturn('anothervendor_alib_1.0.0');
        $anotherVendorALib->getPackagePath(Argument::any())->willReturn('not/existing/path/because/it/is/a/dev/dependency');

        $composer = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Composer');
        $composer->getPackages()->willReturn(array($aVendorALib, $aVendorAnotherLib, $anotherVendorALib));

        $cache = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Cache');
        $cache->has('vendor_alib_1.0.0')->willReturn(true);
        $cache->getCachedRuleSet('vendor_alib_1.0.0')->willReturn($aVendorALibRuleSet);
        $cache->has('vendor_anotherlib_1.0.0')->willReturn(false);
        $cache->has('anothervendor_alib_1.0.0')->willReturn(false);

        $traverser = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser');
        $traverser->traverse(vfsStream::url('root/vendor/avendor/anotherlib'))->willReturn($aVendorAnotherLibRuleSet);
        $cache->cacheRuleSet('vendor_anotherlib_1.0.0', $aVendorAnotherLibRuleSet)->shouldBeCalled();

        $factory = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\ComposerFactory');
        $factory->fromLock(vfsStream::url('composer.lock'))->willReturn($composer);

        $loader = new ComposerLoader($traverser->reveal(), $cache->reveal(), $factory->reveal());
        $loader->loadRuleSet(vfsStream::url('composer.lock'));
    }
}
