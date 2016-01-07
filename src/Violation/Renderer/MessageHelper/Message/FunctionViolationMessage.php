<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\FunctionUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class FunctionViolationMessage extends BaseViolationMessage
{
    const USAGE_NAME = 'SensioLabs\DeprecationDetector\FileInfo\Usage\FunctionUsage';

    public function __construct()
    {
        parent::__construct(self::USAGE_NAME);
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
