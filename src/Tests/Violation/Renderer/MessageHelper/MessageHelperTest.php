<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper;

use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper;

class MessageHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $messageHelper = new MessageHelper();

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper', $messageHelper);
    }

    public function testAddViolationMessageAndGetViolationMessage()
    {
        $usage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface');

        $violation = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Violation');
        $violation->getUsage()->willReturn($usage->reveal());
        $violation = $violation->reveal();

        $violationMessage = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\BaseViolationMessage');
        $violationMessage->supports($usage)->willReturn(false);
        $violationMessage->message($usage)->shouldNotBeCalled();

        $anotherViolationMessage = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\BaseViolationMessage');
        $anotherViolationMessage->supports($usage)->willReturn(true);
        $anotherViolationMessage->message($usage)->willReturn('some deprecated things used');

        $messageHelper = new MessageHelper();
        $messageHelper->addViolationMessage($violationMessage->reveal());
        $messageHelper->addViolationMessage($anotherViolationMessage->reveal());

        $this->assertSame('some deprecated things used', $messageHelper->getViolationMessage($violation));
    }

    public function testFallbackMessage()
    {
        $usage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage');
        $usage->name()->willReturn('SomeClass');

        $violation = $this->prophesize('SensioLabs\DeprecationDetector\Violation\Violation');
        $violation->getUsage()->willReturn($usage->reveal());

        $messageHelper = new MessageHelper();
        $this->assertRegExp('#Deprecated P[0-9]+ <info>SomeClass</info>#', $messageHelper->getViolationMessage($violation->reveal()));
    }
}
