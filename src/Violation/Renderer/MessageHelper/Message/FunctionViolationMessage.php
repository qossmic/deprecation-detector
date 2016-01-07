<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\FunctionUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class FunctionViolationMessage extends BaseViolationMessage implements ViolationMessageInterface
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
        if (!$usage instanceof FunctionUsage) {
            return '';
        }

        return sprintf(
        'Using deprecated function <info>%s()</info>',
        $usage->name()
    );
    }
}
