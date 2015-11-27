<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class LanguageDeprecationMessage extends BaseViolationMessage implements ViolationMessageInterface
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
        if (!$usage instanceof DeprecatedLanguageUsage) {
            return '';
        }

        return sprintf(
            'Using deprecated language feature <info>%s</info>',
            $usage->name()
        );
    }
}
