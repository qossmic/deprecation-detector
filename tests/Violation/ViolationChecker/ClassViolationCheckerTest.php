<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\ClassViolationChecker;

class ClassViolationCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $ruleSet = $this->prophesize(RuleSet::class);
        $checker = new ClassViolationChecker($ruleSet->reveal());

        $this->assertInstanceOf(
            ClassViolationChecker::class,
            $checker
        );
    }

    public function testCheck()
    {
        $classUsage = $this->prophesize(ClassUsage::class);
        $classUsage->name()->willReturn('class');

        $deprecatedClassUsage = $this->prophesize(ClassUsage::class);
        $deprecatedClassUsage->name()->willReturn('deprecatedClass');
        $deprecatedClassUsage->getLineNumber()->willReturn(10);

        $classUsage = $classUsage->reveal();
        $deprecatedClassUsage = $deprecatedClassUsage->reveal();

        $ruleSet = $this->prophesize(RuleSet::class);
        $collection = $this->prophesize(PhpFileInfo::class);
        $collection->classUsages()->willReturn([
            $classUsage,
            $deprecatedClassUsage,
        ]);
        $collection = $collection->reveal();

        $ruleSet->hasClass('class')->willReturn(false);
        $ruleSet->hasClass('deprecatedClass')->willReturn(true);

        $deprecationComment = 'comment';
        $classDeprecation = $this->prophesize(ClassDeprecation::class);
        $classDeprecation->comment()->willReturn($deprecationComment);

        $ruleSet->getClass('deprecatedClass')->willReturn($classDeprecation->reveal());

        $checker = new ClassViolationChecker();

        $this->assertEquals([
            new Violation($deprecatedClassUsage, $collection, $deprecationComment),
        ], $checker->check($collection, $ruleSet->reveal()));
    }
}
