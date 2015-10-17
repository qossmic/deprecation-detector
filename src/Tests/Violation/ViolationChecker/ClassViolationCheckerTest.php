<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\ClassViolationChecker;

class ClassViolationCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $checker = new ClassViolationChecker($ruleSet->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Violation\ViolationChecker\ClassViolationChecker', $checker);
    }

    public function testCheck()
    {
        $classUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage');
        $classUsage->name()->willReturn('class');

        $deprecatedClassUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage');
        $deprecatedClassUsage->name()->willReturn('deprecatedClass');
        $deprecatedClassUsage->getLineNumber()->willReturn(10);

        $classUsage = $classUsage->reveal();
        $deprecatedClassUsage = $deprecatedClassUsage->reveal();

        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $collection = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');
        $collection->classUsages()->willReturn(array(
            $classUsage,
            $deprecatedClassUsage,
        ));
        $collection = $collection->reveal();

        $ruleSet->hasClass('class')->willReturn(false);
        $ruleSet->hasClass('deprecatedClass')->willReturn(true);

        $deprecationComment = 'comment';
        $classDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation');
        $classDeprecation->comment()->willReturn($deprecationComment);

        $ruleSet->getClass('deprecatedClass')->willReturn($classDeprecation->reveal());

        $checker = new ClassViolationChecker($ruleSet->reveal());

        $this->assertEquals(array(
            new Violation($deprecatedClassUsage, $collection, $deprecationComment),
        ), $checker->check($collection));
    }
}
