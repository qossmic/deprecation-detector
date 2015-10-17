<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\SuperTypeViolationChecker;

class SuperTypeViolationCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $checker = new SuperTypeViolationChecker($ruleSet->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Violation\ViolationChecker\SuperTypeViolationChecker', $checker);
    }

    public function testCheck()
    {
        $superTypeUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage');
        $superTypeUsage->name()->willReturn('class');

        $deprecatedSuperTypeUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage');
        $deprecatedSuperTypeUsage->name()->willReturn('deprecatedClass');
        $deprecatedSuperTypeUsage->className()->willReturn('someClass');
        $deprecatedSuperTypeUsage->getLineNumber()->willReturn(10);

        $superTypeUsage = $superTypeUsage->reveal();
        $deprecatedSuperTypeUsage = $deprecatedSuperTypeUsage->reveal();

        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $file = $this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal();
        $collection = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');
        $collection->superTypeUsages()->willReturn(array(
            $superTypeUsage,
            $deprecatedSuperTypeUsage,
        ));
        $collection = $collection->reveal();

        $ruleSet->hasClass('class')->willReturn(false);
        $ruleSet->hasClass('deprecatedClass')->willReturn(true);

        $deprecationComment = 'comment';
        $classDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation');
        $classDeprecation->comment()->willReturn($deprecationComment);

        $ruleSet->getClass('deprecatedClass')->willReturn($classDeprecation->reveal());

        $checker = new SuperTypeViolationChecker($ruleSet->reveal());

        $this->assertEquals(array(
            new Violation($deprecatedSuperTypeUsage, $collection, $deprecationComment),
        ), $checker->check($collection));
    }
}
