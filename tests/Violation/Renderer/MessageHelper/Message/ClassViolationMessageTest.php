<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\ClassViolationMessage;

class ClassViolationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $message = new ClassViolationMessage(ClassUsage::class);

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\ClassViolationMessage',
            $message
        );
    }

    public function testMessageWithSupportedUsage()
    {
        $classUsage = $this->prophesize(ClassUsage::class);
        $classUsage->name()->willReturn('SomeClass');
        $message = new ClassViolationMessage(ClassUsage::class);

        $this->assertSame('Using deprecated class <info>SomeClass</info>', $message->message($classUsage->reveal()));
    }

    public function testMessageWithUnsupportedMessage()
    {
        $interfaceUsage = $this->prophesize(InterfaceUsage::class);
        $interfaceUsage->name()->willReturn('SomeClass');
        $message = new ClassViolationMessage(ClassUsage::class);

        $this->assertSame('', $message->message($interfaceUsage->reveal()));
    }
}
