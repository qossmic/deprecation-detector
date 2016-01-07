<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class SuperTypeViolationMessage extends BaseViolationMessage
{
    const USAGE_NAME = 'SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage';

    public function __construct()
    {
        parent::__construct(self::USAGE_NAME);
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
