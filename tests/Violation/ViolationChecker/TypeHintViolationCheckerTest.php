<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\Deprecation\InterfaceDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\TypeHintUsage;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\TypeHintViolationChecker;

class TypeHintViolationCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $ruleSet = $this->prophesize(RuleSet::class);
        $checker = new TypeHintViolationChecker($ruleSet->reveal());

        $this->assertInstanceOf(
            TypeHintViolationChecker::class,
            $checker
        );
    }

    public function testClassTypeHintCheck()
    {
        $typeHintUsage = $this->prophesize(TypeHintUsage::class);
        $typeHintUsage->name()->willReturn('TypeHint');
        $typeHintUsage->getLineNumber()->willReturn(123);
        $typeHintUsage = $typeHintUsage->reveal();

        $ruleSet = $this->prophesize(RuleSet::class);
        $phpFileInfo = $this->prophesize(PhpFileInfo::class);
        $phpFileInfo->typeHintUsages()->willReturn([$typeHintUsage]);
        $phpFileInfo = $phpFileInfo->reveal();

        $classDeprecation = $this->prophesize(ClassDeprecation::class);
        $classDeprecation->comment()->willReturn('comment');

        $ruleSet->hasClass('TypeHint')->willReturn(true);
        $ruleSet->getClass('TypeHint')->willReturn($classDeprecation->reveal());

        $checker = new TypeHintViolationChecker();
        $usage = new ClassUsage('TypeHint', 123);

        $this->assertEquals([
            new Violation($usage, $phpFileInfo, 'comment'),
        ], $checker->check($phpFileInfo, $ruleSet->reveal()));
    }

    public function testInterfaceTypeHintCheck()
    {
        $typeHintUsage = $this->prophesize(TypeHintUsage::class);
        $typeHintUsage->name()->willReturn('TypeHint');
        $typeHintUsage->getLineNumber()->willReturn(123);
        $typeHintUsage = $typeHintUsage->reveal();

        $ruleSet = $this->prophesize(RuleSet::class);
        $phpFileInfo = $this->prophesize(PhpFileInfo::class);
        $phpFileInfo->typeHintUsages()->willReturn([$typeHintUsage]);
        $phpFileInfo = $phpFileInfo->reveal();

        $interfaceDeprecation = $this
            ->prophesize(InterfaceDeprecation::class);
        $interfaceDeprecation->comment()->willReturn('comment');

        $ruleSet->hasClass('TypeHint')->willReturn(false);
        $ruleSet->hasInterface('TypeHint')->willReturn(true);
        $ruleSet->getInterface('TypeHint')->willReturn($interfaceDeprecation->reveal());

        $checker = new TypeHintViolationChecker();
        $usage = new InterfaceUsage('TypeHint', '', 123);

        $this->assertEquals([
            new Violation($usage, $phpFileInfo, 'comment'),
        ], $checker->check($phpFileInfo, $ruleSet->reveal()));
    }
}
