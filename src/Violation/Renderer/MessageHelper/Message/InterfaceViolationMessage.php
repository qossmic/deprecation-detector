<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class InterfaceViolationMessage extends BaseViolationMessage implements ViolationMessageInterface
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
        if (!$usage instanceof InterfaceUsage) {
            return '';
        }

        $violationInfo = sprintf(
            'Using deprecated interface <info>%s</info>',
            $usage->name()
        );

        $className = $usage->className();

        return (empty($className) ? $violationInfo : sprintf('%s by class <info>%s</info>', $violationInfo, $className));
    }
}
