<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class LanguageDeprecationMessage extends BaseViolationMessage
{
    const USAGE_NAME = 'SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage';

    public function __construct()
    {
        parent::__construct(self::USAGE_NAME);
    }

    public function message(UsageInterface $usage)
    {
        if (!$usage instanceof DeprecatedLanguageUsage) {
            return '';
        }

        return sprintf(
            'Using deprecated language feature <info>%s</info>',
            $usage->name()
        );
    }
}
