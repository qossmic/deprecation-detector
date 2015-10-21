<?php

namespace SensioLabs\DeprecationDetector\Tests\Finder;

use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;

class ParsedPhpFileFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $finder = new ParsedPhpFileFinder();

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder', $finder);
    }
}
