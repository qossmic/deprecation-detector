<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\InterfaceViolationMessage;

class InterfaceViolationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $message = new InterfaceViolationMessage(InterfaceUsage::class);

        $this->assertInstanceOf(
            InterfaceViolationMessage::class,
            $message
        );
    }

    public function testMessageWithSupportedUsage()
    {
        $interfaceUsage = $this->prophesize(InterfaceUsage::class);
        $interfaceUsage->name()->willReturn('SomeInterface');
        $interfaceUsage->className()->willReturn(null);
        $message = new InterfaceViolationMessage(InterfaceUsage::class);

        $this->assertSame(
            'Using deprecated interface <info>SomeInterface</info>',
            $message->message($interfaceUsage->reveal())
        );

        $interfaceUsage = $this->prophesize(InterfaceUsage::class);
        $interfaceUsage->name()->willReturn('SomeInterface');
        $interfaceUsage->className()->willReturn('SomeClass');

        $this->assertSame(
            'Using deprecated interface <info>SomeInterface</info> by class <info>SomeClass</info>',
            $message->message($interfaceUsage->reveal())
        );
    }

    public function testMessageWithUnsupportedMessage()
    {
        $classUsage = $this->prophesize(ClassUsage::class);
        $message = new InterfaceViolationMessage(ClassUsage::class);

        $this->assertSame('', $message->message($classUsage->reveal()));
    }
}
