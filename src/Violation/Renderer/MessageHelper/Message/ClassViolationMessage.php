<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class ClassViolationMessage extends BaseViolationMessage implements ViolationMessageInterface
{
    /**
     * @param string $usageName
     */
    public function __construct($usageName)
    {
        parent::__construct($usageName);
    }

    public function message(UsageInterface $usage)
    {
        if (!$usage instanceof ClassUsage) {
            return '';
        }

        return sprintf(
            'Using deprecated class <info>%s</info>',
            $usage->name()
        );
    }
}
