<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\FunctionViolationMessage;

class FunctionViolationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $message = new FunctionViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\FunctionUsage');

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\FunctionViolationMessage',
            $message
        );
    }

    public function testMessageWithSupportedUsage()
    {
        $functionUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\FunctionUsage');
        $functionUsage->name()->willReturn('someFunction');
        $message = new FunctionViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\FunctionUsage');

        $this->assertSame('Using deprecated function <info>someFunction()</info>', $message->message($functionUsage->reveal()));
    }

    public function testMessageWithUnsupportedMessage()
    {
        $interfaceUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage');
        $interfaceUsage->name()->willReturn('SomeClass');
        $message = new FunctionViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\FunctionUsage');

        $this->assertSame('', $message->message($interfaceUsage->reveal()));
    }
}
