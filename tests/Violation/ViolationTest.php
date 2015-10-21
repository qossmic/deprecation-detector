<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation;

use SensioLabs\DeprecationDetector\Violation\Violation;

class ViolationTest extends \PHPUnit_Framework_TestCase
{
    public function testInitializing()
    {
        $usage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface');
        $file = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');

        $violation = new Violation($usage->reveal(), $file->reveal(), 'comment');

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Violation\Violation', $violation);
    }

    public function testGetters()
    {
        $usage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface');
        $usage->getLineNumber()->willReturn(10);
        $usage = $usage->reveal();
        $file = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');
        $file = $file->reveal();

        $violation = new Violation($usage, $file, 'comment');

        $this->assertEquals(10, $violation->getLine());
        $this->assertEquals($usage, $violation->getUsage());
        $this->assertEquals($file, $violation->getFile());
        $this->assertEquals('comment', $violation->getComment());
    }
}
