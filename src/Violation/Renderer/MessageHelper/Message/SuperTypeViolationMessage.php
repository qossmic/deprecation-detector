<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class SuperTypeViolationMessage extends BaseViolationMessage implements ViolationMessageInterface
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
        if (!$usage instanceof SuperTypeUsage) {
            return '';
        }

        return sprintf(
            'Extending deprecated class <info>%s</info> by class <info>%s</info>',
            $usage->name(),
            $usage->className()
        );
    }
}
