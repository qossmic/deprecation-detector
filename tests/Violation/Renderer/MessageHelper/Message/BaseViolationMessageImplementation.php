<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\BaseViolationMessage;

class BaseViolationMessageImplementation extends BaseViolationMessage
{
    public function message(UsageInterface $usage)
    {
    }
}
