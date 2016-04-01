<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;

class BaseViolationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testSupports()
    {
        $usage = $this->prophesize(ClassUsage::class);
        $baseViolationMessage
            = new BaseViolationMessageImplementation(ClassUsage::class);

        $this->assertTrue($baseViolationMessage->supports($usage->reveal()));

        $usage = $this->prophesize(InterfaceUsage::class);
        $this->assertFalse($baseViolationMessage->supports($usage->reveal()));
    }
}
