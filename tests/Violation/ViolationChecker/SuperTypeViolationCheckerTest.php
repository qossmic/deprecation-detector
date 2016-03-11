<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\SuperTypeViolationChecker;

class SuperTypeViolationCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $ruleSet = $this->prophesize(RuleSet::class);
        $checker = new SuperTypeViolationChecker($ruleSet->reveal());

        $this->assertInstanceOf(
            SuperTypeViolationChecker::class,
            $checker
        );
    }

    public function testCheck()
    {
        $superTypeUsage = $this->prophesize(SuperTypeUsage::class);
        $superTypeUsage->name()->willReturn('class');

        $deprecatedSuperTypeUsage = $this->prophesize(SuperTypeUsage::class);
        $deprecatedSuperTypeUsage->name()->willReturn('deprecatedClass');
        $deprecatedSuperTypeUsage->className()->willReturn('someClass');
        $deprecatedSuperTypeUsage->getLineNumber()->willReturn(10);

        $superTypeUsage = $superTypeUsage->reveal();
        $deprecatedSuperTypeUsage = $deprecatedSuperTypeUsage->reveal();

        $ruleSet = $this->prophesize(RuleSet::class);
        $collection = $this->prophesize(PhpFileInfo::class);
        $collection->superTypeUsages()->willReturn(array(
            $superTypeUsage,
            $deprecatedSuperTypeUsage,
        ));
        $collection = $collection->reveal();

        $ruleSet->hasClass('class')->willReturn(false);
        $ruleSet->hasClass('deprecatedClass')->willReturn(true);

        $deprecationComment = 'comment';
        $classDeprecation = $this->prophesize(ClassDeprecation::class);
        $classDeprecation->comment()->willReturn($deprecationComment);

        $ruleSet->getClass('deprecatedClass')->willReturn($classDeprecation->reveal());

        $checker = new SuperTypeViolationChecker();

        $this->assertEquals(array(
            new Violation($deprecatedSuperTypeUsage, $collection, $deprecationComment),
        ), $checker->check($collection, $ruleSet->reveal()));
    }
}
