<?php

namespace SensioLabs\DeprecationDetector\Tests\RuleSet\Loader\Composer;

use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Composer;

class ComposerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $composer = new Composer(array(), array(), false);

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Composer', $composer);
    }

    public function testGetPackagesWithDevPackages()
    {
        $aPackage = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Package')->reveal();
        $anotherPackage = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Package')->reveal();

        $composer = new Composer(array($aPackage), array($anotherPackage), true);
        $this->assertEquals(array($aPackage, $anotherPackage), $composer->getPackages());
    }

    public function testGetPackagesWithoutDevPackages()
    {
        $aPackage = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Package')->reveal();
        $anotherPackage = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Package')->reveal();

        $composer = new Composer(array($aPackage), array($anotherPackage), false);
        $this->assertEquals(array($aPackage), $composer->getPackages());
    }
}
