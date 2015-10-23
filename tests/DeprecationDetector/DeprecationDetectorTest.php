<?php

namespace SensioLabs\DeprecationDetector\DeprecationDetector\Tests;

use SensioLabs\DeprecationDetector\DeprecationDetector\DeprecationDetector;

class DeprecationDetectorTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $ruleSetLoader = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\LoaderInterface');
        $ancestorResolver = $this->prophesize('SensioLabs\DeprecationDetector\AncestorResolver');
        $deprecationParser = $this->prophesize('SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder');
        $violationDetector = $this->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationDetector');
        $renderer = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Renderer\RendererInterface');

        $detector = new DeprecationDetector(
            $ruleSetLoader->reveal(),
            $ancestorResolver->reveal(),
            $deprecationParser->reveal(),
            $violationDetector->reveal(),
            $renderer->reveal()
        );

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\DeprecationDetector\DeprecationDetector', $detector);
    }
}