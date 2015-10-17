<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\InterfaceViolationMessage;

class InterfaceViolationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $message = new InterfaceViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage');

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\InterfaceViolationMessage', $message);
    }

    public function testMessageWithSupportedUsage()
    {
        $interfaceUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage');
        $interfaceUsage->name()->willReturn('SomeInterface');
        $interfaceUsage->className()->willReturn(null);
        $message = new InterfaceViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage');

        $this->assertSame('Using deprecated interface <info>SomeInterface</info>', $message->message($interfaceUsage->reveal()));

        $interfaceUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage');
        $interfaceUsage->name()->willReturn('SomeInterface');
        $interfaceUsage->className()->willReturn('SomeClass');

        $this->assertSame('Using deprecated interface <info>SomeInterface</info> by class <info>SomeClass</info>', $message->message($interfaceUsage->reveal()));
    }

    public function testMessageWithUnsupportedMessage()
    {
        $classUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage');
        $message = new InterfaceViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage');

        $this->assertSame('', $message->message($classUsage->reveal()));
    }
}
