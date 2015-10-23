<?php

namespace SensioLabs\DeprecationDetector\DeprecationDetector\Tests;

use SensioLabs\DeprecationDetector\DeprecationDetector\DeprecationDetector;

class DeprecationDetectorTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $ruleSetLoader = $this->prophesize('SensioLabs\DeprecationDetector\RuleSet\Loader\LoaderInterface');
        $deprecationParser = $this->prophesize('SensioLabs\DeprecationDetector\Parser\ParserInterface');
        $violationDetector = $this->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationDetector');
        $renderer = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Renderer\RendererInterface');

        $detector = new DeprecationDetector(
            $ruleSetLoader->reveal(),
            $deprecationParser->reveal(),
            $violationDetector->reveal(),
            $renderer->reveal()
        );

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\DeprecationDetector\DeprecationDetector', $detector);
    }
}
