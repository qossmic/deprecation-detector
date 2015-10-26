<?php

namespace SensioLabs\DeprecationDetector\Tests\Finder;

use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;

class ParsedPhpFileFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $parser = $this->prophesize('SensioLabs\DeprecationDetector\Parser\ParserInterface');
        $progressOutput = $this->prophesize(
            'SensioLabs\DeprecationDetector\DeprecationDetector\Output\VerboseProgressOutput'
        );

        $finder = new ParsedPhpFileFinder(
            $parser->reveal(),
            $progressOutput->reveal()
        );

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder', $finder);
    }
}
