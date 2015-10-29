<?php

namespace SensioLabs\DeprecationDetector\Tests;

use SensioLabs\DeprecationDetector\DeprecationDetector;

class DeprecationDetectorTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $ruleSetLoader = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\LoaderInterface');
        $ancestorResolver = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\AncestorResolver');
        $deprecationParser = $this->prophesize('SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder');
        $violationDetector = $this->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationDetector');
        $renderer = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Renderer\RendererInterface');
        $defaultOutput = $this->prophesize(
            'SensioLabs\DeprecationDetector\Console\Output\DefaultProgressOutput'
        );

        $detector = new DeprecationDetector(
            $ruleSetLoader->reveal(),
            $ancestorResolver->reveal(),
            $deprecationParser->reveal(),
            $violationDetector->reveal(),
            $renderer->reveal(),
            $defaultOutput->reveal()
        );

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\DeprecationDetector', $detector);
    }
}
