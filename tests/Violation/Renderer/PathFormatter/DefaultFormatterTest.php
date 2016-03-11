<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\PathFormatter;

use SensioLabs\DeprecationDetector\Violation\Renderer\PathFormatter\DefaultFormatter;

class DefaultFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $formatter = new DefaultFormatter();

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\Violation\Renderer\PathFormatter\DefaultFormatter',
            $formatter
        );
    }

    public function testFormatReturnsOriginalString()
    {
        $formatter = new DefaultFormatter();

        $this->assertEquals(
            'path/to/file',
            $formatter->format('path/to/file')
        );
    }
}
