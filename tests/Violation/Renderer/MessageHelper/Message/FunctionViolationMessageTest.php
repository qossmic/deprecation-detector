<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\FunctionUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\FunctionViolationMessage;

class FunctionViolationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $message = new FunctionViolationMessage(FunctionUsage::class);

        $this->assertInstanceOf(
            FunctionViolationMessage::class,
            $message
        );
    }

    public function testMessageWithSupportedUsage()
    {
        $functionUsage = $this->prophesize(FunctionUsage::class);
        $functionUsage->name()->willReturn('someFunction');
        $message = new FunctionViolationMessage(FunctionUsage::class);

        $this->assertSame('Using deprecated function <info>someFunction()</info>', $message->message($functionUsage->reveal()));
    }

    public function testMessageWithUnsupportedMessage()
    {
        $interfaceUsage = $this->prophesize(InterfaceUsage::class);
        $interfaceUsage->name()->willReturn('SomeClass');
        $message = new FunctionViolationMessage(FunctionUsage::class);

        $this->assertSame('', $message->message($interfaceUsage->reveal()));
    }
}
