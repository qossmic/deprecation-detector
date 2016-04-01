<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\SuperTypeViolationMessage;

class SuperTypeViolationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $message = new SuperTypeViolationMessage(SuperTypeUsage::class);

        $this->assertInstanceOf(
            SuperTypeViolationMessage::class,
            $message
        );
    }

    public function testMessageWithSupportedUsage()
    {
        $superTypeUsage = $this->prophesize(SuperTypeUsage::class);
        $superTypeUsage->name()->willReturn('SomeSuperType');
        $superTypeUsage->className()->willReturn('SomeClass');
        $message = new SuperTypeViolationMessage(SuperTypeUsage::class);

        $this->assertSame(
            'Extending deprecated class <info>SomeSuperType</info> by class <info>SomeClass</info>',
            $message->message($superTypeUsage->reveal())
        );
    }

    public function testMessageWithUnsupportedMessage()
    {
        $classUsage = $this->prophesize(ClassUsage::class);
        $message = new SuperTypeViolationMessage(SuperTypeUsage::class);

        $this->assertSame('', $message->message($classUsage->reveal()));
    }
}
