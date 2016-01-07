<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class ClassViolationMessage extends BaseViolationMessage
{
    const USAGE_NAME = 'SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage';

    public function __construct()
    {
        parent::__construct(self::USAGE_NAME);
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
