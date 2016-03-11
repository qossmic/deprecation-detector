<?php

namespace SensioLabs\DeprecationDetector\Tests;

use Prophecy\Argument;
use SensioLabs\DeprecationDetector\Console\Output\DefaultProgressOutput;
use SensioLabs\DeprecationDetector\DeprecationDetector;
use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;
use SensioLabs\DeprecationDetector\Finder\Result;
use SensioLabs\DeprecationDetector\RuleSet\Loader\LoaderInterface;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\TypeGuessing\AncestorResolver;
use SensioLabs\DeprecationDetector\Violation\Renderer\RendererInterface;
use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationDetector;

class DeprecationDetectorTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $preDefinedRuleSet = $this->prophesize(RuleSet::class);
        $ruleSetLoader = $this->prophesize(LoaderInterface::class);
        $ancestorResolver = $this->prophesize(AncestorResolver::class);
        $deprecationFinder = $this->prophesize(ParsedPhpFileFinder::class);
        $violationDetector = $this->prophesize(ViolationDetector::class);
        $renderer = $this->prophesize(RendererInterface::class);
        $defaultOutput = $this->prophesize(
            DefaultProgressOutput::class
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

        $this->assertInstanceOf(DeprecationDetector::class, $detector);
    }

    public function testCheckForDeprecations()
    {
        $preDefinedRuleSet = $this->prophesize(RuleSet::class);

        $sourceArg = 'path/to/ruleset';
        $ruleSetArg = 'path/to/source/code';
        $fileCount = 10;
        $violationCount = 2;

        $ruleSet = $this->prophesize(RuleSet::class);
        $ruleSet->merge($preDefinedRuleSet->reveal())->shouldBeCalled();
        $ruleSetLoader = $this->prophesize(LoaderInterface::class);
        $ruleSetLoader->loadRuleSet($ruleSetArg)->willReturn($ruleSet->reveal());

        $ancestorResolver = $this->prophesize(AncestorResolver::class);
        $ancestorResolver->setSourcePaths(Argument::any())->shouldBeCalled();

        $deprecationResult = $this->prophesize(Result::class);
        $deprecationResult->parsedFiles()->willReturn($parsedFiles = array());
        $deprecationResult->fileCount()->willReturn($fileCount);
        $deprecationResult->parserErrors()->willReturn(array());

        $deprecationFinder = $this->prophesize(ParsedPhpFileFinder::class);
        $deprecationFinder->parsePhpFiles($sourceArg)->willReturn($deprecationResult->reveal());

        $aViolation = $this->prophesize(Violation::class);
        $anotherViolation = $this->prophesize(Violation::class);
        $violations = array(
            $aViolation->reveal(),
            $anotherViolation->reveal(),
        );

        $violationDetector = $this->prophesize(ViolationDetector::class);
        $violationDetector->getViolations($ruleSet->reveal(), $parsedFiles)->willReturn($violations);

        $renderer = $this->prophesize(RendererInterface::class);
        $renderer->renderViolations($violations, array())->shouldBeCalled();

        $defaultOutput = $this->prophesize(
            DefaultProgressOutput::class
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
