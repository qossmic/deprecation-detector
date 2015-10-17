<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\SuperTypeViolationMessage;

class SuperTypeViolationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $message = new SuperTypeViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage');

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\SuperTypeViolationMessage', $message);
    }

    public function testMessageWithSupportedUsage()
    {
        $superTypeUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage');
        $superTypeUsage->name()->willReturn('SomeSuperType');
        $superTypeUsage->className()->willReturn('SomeClass');
        $message = new SuperTypeViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage');

        $this->assertSame('Extending deprecated class <info>SomeSuperType</info> by class <info>SomeClass</info>', $message->message($superTypeUsage->reveal()));
    }

    public function testMessageWithUnsupportedMessage()
    {
        $classUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage');
        $message = new SuperTypeViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage');

        $this->assertSame('', $message->message($classUsage->reveal()));
    }
}
