<?php

namespace SensioLabs\DeprecationDetector\Tests;

use Prophecy\Argument;
use SensioLabs\DeprecationDetector\DeprecationDetector;

class DeprecationDetectorTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $preDefinedRuleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $sourceRuleSetLoader = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\DirectoryLoader');
        $ruleSetLoader = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\LoaderInterface');
        $ancestorResolver = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\AncestorResolver');
        $deprecationFinder = $this->prophesize('SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder');
        $violationDetector = $this->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationDetector');
        $renderer = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Renderer\RendererInterface');
        $defaultOutput = $this->prophesize(
            'SensioLabs\DeprecationDetector\Console\Output\DefaultProgressOutput'
        );

        $detector = new DeprecationDetector(
            $preDefinedRuleSet->reveal(),
            $sourceRuleSetLoader->reveal(),
            $ruleSetLoader->reveal(),
            $ancestorResolver->reveal(),
            $deprecationFinder->reveal(),
            $violationDetector->reveal(),
            $renderer->reveal(),
            $defaultOutput->reveal()
        );

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\DeprecationDetector', $detector);
    }

    public function testCheckForDeprecations()
    {
        $preDefinedRuleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');

        $sourceArg = 'path/to/ruleset';
        $ruleSetArg = 'path/to/source/code';
        $fileCount = 10;
        $violationCount = 2;

        $sourceRuleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $sourceRuleSetLoader = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\DirectoryLoader');
        $sourceRuleSetLoader->loadRuleSet($sourceArg)->willReturn($sourceRuleSet->reveal());

        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $ruleSet->merge($preDefinedRuleSet->reveal())->shouldBeCalled();
        $ruleSet->merge($sourceRuleSet->reveal())->shouldBeCalled();
        $ruleSetLoader = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\LoaderInterface');
        $ruleSetLoader->loadRuleSet($ruleSetArg)->willReturn($ruleSet->reveal());

        $ancestorResolver = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\AncestorResolver');
        $ancestorResolver->setSourcePaths(Argument::any())->shouldBeCalled();

        $deprecationResult = $this->prophesize('SensioLabs\DeprecationDetector\Finder\Result');
        $deprecationResult->parsedFiles()->willReturn($parsedFiles = array());
        $deprecationResult->fileCount()->willReturn($fileCount);
        $deprecationResult->parserErrors()->willReturn(array());

        $deprecationFinder = $this->prophesize('SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder');
        $deprecationFinder->parsePhpFiles($sourceArg)->willReturn($deprecationResult->reveal());

        $aViolation = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Violation');
        $anotherViolation = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Violation');
        $violations = array(
            $aViolation->reveal(),
            $anotherViolation->reveal(),
        );

        $violationDetector = $this->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationDetector');
        $violationDetector->getViolations($ruleSet->reveal(), $parsedFiles)->willReturn($violations);

        $renderer = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Renderer\RendererInterface');
        $renderer->renderViolations($violations, array())->shouldBeCalled();

        $defaultOutput = $this->prophesize(
            'SensioLabs\DeprecationDetector\Console\Output\DefaultProgressOutput'
        );
        $defaultOutput->startProgress()->shouldBeCalled();
        $defaultOutput->startRuleSetGeneration()->shouldBeCalled();
        $defaultOutput->endRuleSetGeneration()->shouldBeCalled();
        $defaultOutput->startUsageDetection()->shouldBeCalled();
        $defaultOutput->endUsageDetection()->shouldBeCalled();
        $defaultOutput->startOutputRendering()->shouldBeCalled();
        $defaultOutput->endOutputRendering()->shouldBeCalled();
        $defaultOutput->endProgress($fileCount, $violationCount)->shouldBeCalled();

        $detector = new DeprecationDetector(
            $preDefinedRuleSet->reveal(),
            $sourceRuleSetLoader->reveal(),
            $ruleSetLoader->reveal(),
            $ancestorResolver->reveal(),
            $deprecationFinder->reveal(),
            $violationDetector->reveal(),
            $renderer->reveal(),
            $defaultOutput->reveal()
        );

        $this->assertSame($violations, $detector->checkForDeprecations($sourceArg, $ruleSetArg));
    }
}
