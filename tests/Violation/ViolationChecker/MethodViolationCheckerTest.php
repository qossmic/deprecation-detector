<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\Deprecation\MethodDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\TypeGuessing\AncestorResolver;
use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\MethodViolationChecker;

class MethodViolationCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $ancestorResolver = $this->prophesize(AncestorResolver::class);
        $checker = new MethodViolationChecker($ancestorResolver->reveal());

        $this->assertInstanceOf(
            MethodViolationChecker::class,
            $checker
        );
    }

    public function testCheck()
    {
        $methodUsage = $this->prophesize(MethodUsage::class);
        $methodUsage->className()->willReturn('class');
        $methodUsage->name()->willReturn('method');

        $deprecatedMethodUsage = $this->prophesize(MethodUsage::class);
        $deprecatedMethodUsage->name()->willReturn('deprecatedMethod');
        $deprecatedMethodUsage->className()->willReturn('class');
        $deprecatedMethodUsage->getLineNumber()->willReturn(10);

        $methodUsage = $methodUsage->reveal();
        $deprecatedMethodUsage = $deprecatedMethodUsage->reveal();

        $ruleSet = $this->prophesize(RuleSet::class);
        $phpFileInfo = $this->prophesize(PhpFileInfo::class);
        $phpFileInfo->methodUsages()->willReturn([
            $methodUsage,
            $deprecatedMethodUsage,
        ]);
        $phpFileInfo->getInterfaceUsageByClass('class')->willReturn([]);
        $phpFileInfo->getSuperTypeUsageByClass('class')->willReturn(null);
        $phpFileInfo = $phpFileInfo->reveal();

        $ruleSet->hasMethod('method', 'class')->willReturn(false);
        $ruleSet->hasMethod('deprecatedMethod', 'class')->willReturn(true);

        $deprecationComment = 'comment';
        $methodDeprecation = $this->prophesize(MethodDeprecation::class);
        $methodDeprecation->comment()->willReturn($deprecationComment);

        $ruleSet->getMethod('deprecatedMethod', 'class')->willReturn($methodDeprecation->reveal());

        $ancestorResolver = $this->prophesize(AncestorResolver::class);
        $ancestorResolver->getClassAncestors($phpFileInfo, 'class')->willReturn([]);

        $checker = new MethodViolationChecker($ancestorResolver->reveal());

        $this->assertEquals(
            [new Violation($deprecatedMethodUsage, $phpFileInfo, $deprecationComment)],
            $checker->check($phpFileInfo, $ruleSet->reveal())
        );
    }
}
