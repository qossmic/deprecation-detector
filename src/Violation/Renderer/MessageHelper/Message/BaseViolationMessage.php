<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

abstract class BaseViolationMessage implements ViolationMessageInterface
{
    protected $usageName;

    /**
     * @param string $usageName
     */
    public function __construct($usageName)
    {
        $this->usageName = $usageName;
    }

    public function supports(UsageInterface $usage)
    {
        if ($usage instanceof $this->usageName) {
            return true;
        }

        return false;
    }
}
