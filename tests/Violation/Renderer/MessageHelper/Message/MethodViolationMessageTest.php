<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\MethodViolationMessage;

class MethodViolationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $message = new MethodViolationMessage(MethodUsage::class);

        $this->assertInstanceOf(
            MethodViolationMessage::class,
            $message
        );
    }

    public function testMessageWithSupportedUsage()
    {
        $methodUsage = $this->prophesize(MethodUsage::class);
        $methodUsage->name()->willReturn('someMethod');
        $methodUsage->className()->willReturn('SomeClass');
        $methodUsage->isStatic()->willReturn(false);
        $message = new MethodViolationMessage(MethodUsage::class);

        $this->assertSame(
            'Calling deprecated method <info>SomeClass->someMethod()</info>',
            $message->message($methodUsage->reveal())
        );

        $methodUsage = $this->prophesize(MethodUsage::class);
        $methodUsage->name()->willReturn('someMethod');
        $methodUsage->className()->willReturn('SomeClass');
        $methodUsage->isStatic()->willReturn(true);

        $this->assertSame(
            'Calling deprecated static method <info>SomeClass::someMethod()</info>',
            $message->message($methodUsage->reveal())
        );
    }

    public function testMessageWithUnsupportedMessage()
    {
        $interfaceUsage = $this->prophesize(InterfaceUsage::class);
        $message = new MethodViolationMessage(ClassUsage::class);

        $this->assertSame('', $message->message($interfaceUsage->reveal()));
    }
}
