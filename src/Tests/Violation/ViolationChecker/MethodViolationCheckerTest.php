<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\MethodViolationChecker;

class MethodViolationCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $ancestorResolver = $this->prophesize('SensioLabs\DeprecationDetector\AncestorResolver');
        $checker = new MethodViolationChecker($ruleSet->reveal(), $ancestorResolver->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Violation\ViolationChecker\MethodViolationChecker', $checker);
    }

    public function testCheck()
    {
        $methodUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage');
        $methodUsage->className()->willReturn('class');
        $methodUsage->name()->willReturn('method');

        $deprecatedMethodUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage');
        $deprecatedMethodUsage->name()->willReturn('deprecatedMethod');
        $deprecatedMethodUsage->className()->willReturn('class');
        $deprecatedMethodUsage->getLineNumber()->willReturn(10);

        $methodUsage = $methodUsage->reveal();
        $deprecatedMethodUsage = $deprecatedMethodUsage->reveal();

        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $phpFileInfo = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');
        $phpFileInfo->methodUsages()->willReturn(array(
            $methodUsage,
            $deprecatedMethodUsage,
        ));
        $phpFileInfo->getInterfaceUsageByClass('class')->willReturn(array());
        $phpFileInfo->getSuperTypeUsageByClass('class')->willReturn(null);
        $phpFileInfo = $phpFileInfo->reveal();

        $ruleSet->hasMethod('method', 'class')->willReturn(false);
        $ruleSet->hasMethod('deprecatedMethod', 'class')->willReturn(true);

        $deprecationComment = 'comment';
        $methodDeprecation = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Deprecation\MethodDeprecation');
        $methodDeprecation->comment()->willReturn($deprecationComment);

        $ruleSet->getMethod('deprecatedMethod', 'class')->willReturn($methodDeprecation->reveal());

        $ancestorResolver = $this->prophesize('SensioLabs\DeprecationDetector\AncestorResolver');
        $ancestorResolver->getClassAncestors($phpFileInfo, 'class')->willReturn(array());

        $checker = new MethodViolationChecker($ruleSet->reveal(), $ancestorResolver->reveal());

        $this->assertEquals(
            array(new Violation($deprecatedMethodUsage, $phpFileInfo, $deprecationComment)),
            $checker->check($phpFileInfo)
        );
    }
}
