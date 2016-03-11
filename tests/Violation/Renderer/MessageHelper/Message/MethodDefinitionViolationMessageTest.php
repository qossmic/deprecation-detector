<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\MethodDefinition;
use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\MethodDefinitionViolationMessage;

class MethodDefinitionViolationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $message = new MethodDefinitionViolationMessage(MethodDefinition::class);

        $this->assertInstanceOf(
            MethodDefinitionViolationMessage::class,
            $message
        );
    }

    public function testMessageWithSupportedUsage()
    {
        $methodDefinition = $this->prophesize(MethodDefinition::class);
        $methodDefinition->parentName()->willReturn('SomeClass');
        $methodDefinition->name()->willReturn('someMethod');
        $message = new MethodDefinitionViolationMessage(MethodDefinition::class);

        $this->assertSame(
            'Overriding deprecated method <info>SomeClass->someMethod()</info>',
            $message->message($methodDefinition->reveal())
        );
    }

    public function testMessageWithUnsupportedMessage()
    {
        $classUsage = $this->prophesize(ClassUsage::class);
        $message = new MethodDefinitionViolationMessage(MethodDefinition::class);

        $this->assertSame('', $message->message($classUsage->reveal()));
    }
}
