<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper;

use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\BaseViolationMessage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper;
use SensioLabs\DeprecationDetector\Violation\Violation;

class MessageHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $messageHelper = new MessageHelper();

        $this->assertInstanceOf(
            MessageHelper::class,
            $messageHelper
        );
    }

    public function testAddViolationMessageAndGetViolationMessage()
    {
        $usage = $this->prophesize(UsageInterface::class);

        $violation = $this->prophesize(Violation::class);
        $violation->getUsage()->willReturn($usage->reveal());
        $violation = $violation->reveal();

        $violationMessage = $this->prophesize(
            BaseViolationMessage::class
        );
        $violationMessage->supports($usage)->willReturn(false);
        $violationMessage->message($usage)->shouldNotBeCalled();

        $anotherViolationMessage = $this->prophesize(
            BaseViolationMessage::class
        );
        $anotherViolationMessage->supports($usage)->willReturn(true);
        $anotherViolationMessage->message($usage)->willReturn('some deprecated things used');

        $messageHelper = new MessageHelper();
        $messageHelper->addViolationMessage($violationMessage->reveal());
        $messageHelper->addViolationMessage($anotherViolationMessage->reveal());

        $this->assertSame('some deprecated things used', $messageHelper->getViolationMessage($violation));
    }

    public function testFallbackMessage()
    {
        $usage = $this->prophesize(ClassUsage::class);
        $usage->name()->willReturn('SomeClass');

        $violation = $this->prophesize(Violation::class);
        $violation->getUsage()->willReturn($usage->reveal());

        $messageHelper = new MessageHelper();
        $this->assertRegExp(
            '#Deprecated P[0-9]+ <info>SomeClass</info>#',
            $messageHelper->getViolationMessage($violation->reveal())
        );
    }
}
