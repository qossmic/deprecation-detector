<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader\Composer;

use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Package;

class PackageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $package = Package::fromArray(array(
            'name' => 'vendor/lib',
            'version' => '1.0.0',
        ));

        $this->assertInstanceOf(Package::class, $package);
    }

    public function testGeneratePackageKey()
    {
        $package = Package::fromArray(array(
            'name' => 'vendor/lib',
            'version' => '1.0.0',
        ));

        $this->assertEquals('vendor_lib_1.0.0', $package->generatePackageKey());
    }

    public function testGetPackagePath()
    {
        $package = Package::fromArray(array(
            'name' => 'vendor/lib',
            'version' => '1.0.0',
        ));

        $this->assertEquals('vendor/vendor/lib', $package->getPackagePath('vendor/'));
    }
}
