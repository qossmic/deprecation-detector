<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class InterfaceViolationMessage extends BaseViolationMessage
{
    const USAGE_NAME = 'SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage';

    public function __construct()
    {
        parent::__construct(self::USAGE_NAME);
    }

    public function message(UsageInterface $usage)
    {
        if (!$usage instanceof InterfaceUsage) {
            return '';
        }

        $violationInfo = sprintf(
            'Using deprecated interface <info>%s</info>',
            $usage->name()
        );

        $className = $usage->className();

        return empty($className)
            ? $violationInfo
            : sprintf('%s by class <info>%s</info>', $violationInfo, $className);
    }
}
