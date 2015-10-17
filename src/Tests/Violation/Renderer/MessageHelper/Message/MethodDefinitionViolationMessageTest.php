<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\MethodDefinitionViolationMessage;

class MethodDefinitionViolationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $message = new MethodDefinitionViolationMessage('SensioLabs\DeprecationDetector\FileInfo\MethodDefinition');

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\MethodDefinitionViolationMessage', $message);
    }

    public function testMessageWithSupportedUsage()
    {
        $methodDefinition = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\MethodDefinition');
        $methodDefinition->parentName()->willReturn('SomeClass');
        $methodDefinition->name()->willReturn('someMethod');
        $message = new MethodDefinitionViolationMessage('SensioLabs\DeprecationDetector\FileInfo\MethodDefinition');

        $this->assertSame('Overriding deprecated method <info>SomeClass->someMethod()</info>', $message->message($methodDefinition->reveal()));
    }

    public function testMessageWithUnsupportedMessage()
    {
        $classUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage');
        $message = new MethodDefinitionViolationMessage('SensioLabs\DeprecationDetector\FileInfo\MethodDefinition');

        $this->assertSame('', $message->message($classUsage->reveal()));
    }
}
