<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader\Composer;

use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Composer;

class ComposerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $composer = new Composer([], [], false);

        $this->assertInstanceOf(Composer::class, $composer);
    }

    public function testGetPackagesWithDevPackages()
    {
        $aPackage = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Package')->reveal();
        $anotherPackage = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Package')->reveal();

        $composer = new Composer([$aPackage], [$anotherPackage], true);
        $this->assertEquals([$aPackage, $anotherPackage], $composer->getPackages());
    }

    public function testGetPackagesWithoutDevPackages()
    {
        $aPackage = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Package')->reveal();
        $anotherPackage = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Package')->reveal();

        $composer = new Composer([$aPackage], [$anotherPackage], false);
        $this->assertEquals([$aPackage], $composer->getPackages());
    }
}
