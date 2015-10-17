<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\BaseViolationMessage;

class BaseViolationMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testSupports()
    {
        $usage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage');
        $baseViolationMessage = new BaseViolationMessageImplementation('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage');

        $this->assertTrue($baseViolationMessage->supports($usage->reveal()));

        $usage = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage');
        $this->assertFalse($baseViolationMessage->supports($usage->reveal()));
    }
}

class BaseViolationMessageImplementation extends BaseViolationMessage
{
    public function __construct($usageName)
    {
        parent::__construct($usageName);
    }

    public function message(UsageInterface $usage)
    {
    }
}
