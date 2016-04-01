<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\LanguageDeprecationMessage;

class LanguageDeprecationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $message = new LanguageDeprecationMessage(DeprecatedLanguageUsage::class);

        $this->assertInstanceOf(
            LanguageDeprecationMessage::class,
            $message
        );
    }

    public function testMessageWithSupportedUsage()
    {
        $deprecatedLanguageUsage = $this->prophesize(DeprecatedLanguageUsage::class);
        $deprecatedLanguageUsage->name()->willReturn('PHP4 constructor');
        $message = new LanguageDeprecationMessage(DeprecatedLanguageUsage::class);

        $this->assertSame('Using deprecated language feature <info>PHP4 constructor</info>', $message->message($deprecatedLanguageUsage->reveal()));
    }

    public function testMessageWithUnsupportedMessage()
    {
        $interfaceUsage = $this->prophesize(InterfaceUsage::class);
        $message = new LanguageDeprecationMessage(DeprecatedLanguageUsage::class);

        $this->assertSame('', $message->message($interfaceUsage->reveal()));
    }
}
