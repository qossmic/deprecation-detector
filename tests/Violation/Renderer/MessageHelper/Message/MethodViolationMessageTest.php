<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\MethodViolationMessage;

class MethodViolationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $message = new MethodViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage');

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\MethodViolationMessage',
            $message
        );
    }

    public function testMessageWithSupportedUsage()
    {
        $methodUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage');
        $methodUsage->name()->willReturn('someMethod');
        $methodUsage->className()->willReturn('SomeClass');
        $methodUsage->isStatic()->willReturn(false);
        $message = new MethodViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage');

        $this->assertSame(
            'Calling deprecated method <info>SomeClass->someMethod()</info>',
            $message->message($methodUsage->reveal())
        );

        $methodUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage');
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
        $interfaceUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage');
        $message = new MethodViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage');

        $this->assertSame('', $message->message($interfaceUsage->reveal()));
    }
}
