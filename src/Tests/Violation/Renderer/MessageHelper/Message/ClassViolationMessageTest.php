<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\ClassViolationMessage;

class ClassViolationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $message = new ClassViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage');

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\ClassViolationMessage', $message);
    }

    public function testMessageWithSupportedUsage()
    {
        $classUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage');
        $classUsage->name()->willReturn('SomeClass');
        $message = new ClassViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage');

        $this->assertSame('Using deprecated class <info>SomeClass</info>', $message->message($classUsage->reveal()));
    }

    public function testMessageWithUnsupportedMessage()
    {
        $interfaceUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage');
        $interfaceUsage->name()->willReturn('SomeClass');
        $message = new ClassViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage');

        $this->assertSame('', $message->message($interfaceUsage->reveal()));
    }
}
