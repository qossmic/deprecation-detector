<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\TypeHintViolationChecker;

class TypeHintViolationCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $checker = new TypeHintViolationChecker($ruleSet->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Violation\ViolationChecker\TypeHintViolationChecker', $checker);
    }

    public function testClassTypeHintCheck()
    {
        $typeHintUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\TypeHintUsage');
        $typeHintUsage->name()->willReturn('TypeHint');
        $typeHintUsage->getLineNumber()->willReturn(123);
        $typeHintUsage = $typeHintUsage->reveal();

        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $phpFileInfo = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');
        $phpFileInfo->typeHintUsages()->willReturn(array($typeHintUsage));
        $phpFileInfo = $phpFileInfo->reveal();

        $classDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation');
        $classDeprecation->comment()->willReturn('comment');

        $ruleSet->hasClass('TypeHint')->willReturn(true);
        $ruleSet->getClass('TypeHint')->willReturn($classDeprecation->reveal());

        $checker = new TypeHintViolationChecker($ruleSet->reveal());
        $usage = new ClassUsage('TypeHint', 123);

        $this->assertEquals(array(
            new Violation($usage, $phpFileInfo, 'comment'),
        ), $checker->check($phpFileInfo));
    }

    public function testInterfaceTypeHintCheck()
    {
        $typeHintUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\TypeHintUsage');
        $typeHintUsage->name()->willReturn('TypeHint');
        $typeHintUsage->getLineNumber()->willReturn(123);
        $typeHintUsage = $typeHintUsage->reveal();

        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $phpFileInfo = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');
        $phpFileInfo->typeHintUsages()->willReturn(array($typeHintUsage));
        $phpFileInfo = $phpFileInfo->reveal();

        $interfaceDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\InterfaceDeprecation');
        $interfaceDeprecation->comment()->willReturn('comment');

        $ruleSet->hasClass('TypeHint')->willReturn(false);
        $ruleSet->hasInterface('TypeHint')->willReturn(true);
        $ruleSet->getInterface('TypeHint')->willReturn($interfaceDeprecation->reveal());

        $checker = new TypeHintViolationChecker($ruleSet->reveal());
        $usage = new InterfaceUsage('TypeHint', '', 123);

        $this->assertEquals(array(
            new Violation($usage, $phpFileInfo, 'comment'),
        ), $checker->check($phpFileInfo));
    }
}
