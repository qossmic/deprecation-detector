<?php

namespace SensioLabs\DeprecationDetector\Tests;

use Prophecy\Argument;
use SensioLabs\DeprecationDetector\DeprecationDetector;

class DeprecationDetectorTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $preDefinedRuleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
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

        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $ruleSet->merge($preDefinedRuleSet->reveal())->shouldBeCalled();
        $ruleSetLoader = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\LoaderInterface');
        $ruleSetLoader->loadRuleSet($ruleSetArg)->willReturn($ruleSet->reveal());

        $ancestorResolver = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\AncestorResolver');
        $ancestorResolver->setSourcePaths(Argument::any())->shouldBeCalled();

        $deprecationFinder = $this->prophesize('SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder');
        $deprecationFinder->in($sourceArg)->willReturn($deprecationFinder->reveal());
        $deprecationFinder->hasParserErrors()->willReturn(false);
        $deprecationFinder->count()->willReturn($fileCount);
        $deprecationFinder->getParserErrors()->willReturn(array());

        $aViolation = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Violation');
        $anotherViolation = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Violation');
        $violations = array(
            $aViolation->reveal(),
            $anotherViolation->reveal(),
        );

        $violationDetector = $this->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationDetector');
        $violationDetector->getViolations($ruleSet->reveal(), $deprecationFinder->reveal())->willReturn($violations);

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
            $ruleSetLoader->reveal(),
            $ancestorResolver->reveal(),
            $deprecationFinder->reveal(),
            $violationDetector->reveal(),
            $renderer->reveal(),
            $defaultOutput->reveal()
        );

        $this->assertSame($violations, $detector->checkForDeprecations($sourceArg, $ruleSetArg));
    }

    public function testCheckForDeprecationsRendersParserErrorsIfThereAreAny()
    {
        $preDefinedRuleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');

        $sourceArg = 'path/to/ruleset';
        $ruleSetArg = 'path/to/source/code';
        $parserErrors = array();
        $fileCount = 10;
        $violationCount = 2;

        $ruleSet = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\RuleSet');
        $ruleSet->merge($preDefinedRuleSet->reveal())->shouldBeCalled();
        $ruleSetLoader = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\LoaderInterface');
        $ruleSetLoader->loadRuleSet($ruleSetArg)->willReturn($ruleSet->reveal());

        $ancestorResolver = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\AncestorResolver');
        $ancestorResolver->setSourcePaths(Argument::any())->shouldBeCalled();

        $deprecationFinder = $this->prophesize('SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder');
        $deprecationFinder->in($sourceArg)->willReturn($deprecationFinder->reveal());
        $deprecationFinder->count()->willReturn($fileCount);
        $deprecationFinder->getParserErrors()->willReturn($parserErrors);

        $aViolation = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Violation');
        $anotherViolation = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Violation');
        $violations = array(
            $aViolation->reveal(),
            $anotherViolation->reveal(),
        );

        $violationDetector = $this->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationDetector');
        $violationDetector->getViolations($ruleSet->reveal(), $deprecationFinder->reveal())->willReturn($violations);

        $renderer = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Renderer\RendererInterface');
        $renderer->renderViolations($violations, $parserErrors)->shouldBeCalled();

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
