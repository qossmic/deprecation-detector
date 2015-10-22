<?php

namespace SensioLabs\DeprecationDetector\DeprecationDetector\Tests;

use SensioLabs\DeprecationDetector\DeprecationDetector\DeprecationDetector;

class DeprecationDetectorTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $parser = $this->prophesize('SensioLabs\DeprecationDetector\Parser\ParserInterface');
        $violationDetector = $this->prophesize('SensioLabs\DeprecationDetector\Violation\ViolationDetector');
        $renderer = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Renderer\RendererInterface');

        $detector = new DeprecationDetector(
            $parser->reveal(),
            $parser->reveal(),
            $violationDetector->reveal(),
            $renderer->reveal()
        );

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\DeprecationDetector\DeprecationDetector', $detector);
    }
}
