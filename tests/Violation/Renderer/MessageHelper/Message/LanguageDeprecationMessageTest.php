<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\LanguageDeprecationMessage;

class LanguageDeprecationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $message = new LanguageDeprecationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage');

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\LanguageDeprecationMessage',
            $message
        );
    }

    public function testMessageWithSupportedUsage()
    {
        $deprecatedLanguageUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage');
        $deprecatedLanguageUsage->name()->willReturn('PHP4 constructor');
        $message = new LanguageDeprecationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage');

        $this->assertSame('Using deprecated language feature <info>PHP4 constructor</info>', $message->message($deprecatedLanguageUsage->reveal()));
    }

    public function testMessageWithUnsupportedMessage()
    {
        $interfaceUsage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage');
        $message = new LanguageDeprecationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage');

        $this->assertSame('', $message->message($interfaceUsage->reveal()));
    }
}
