<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class MethodViolationMessage extends BaseViolationMessage implements ViolationMessageInterface
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
        if (!$usage instanceof MethodUsage) {
            return '';
        }

        $message = ($usage->isStatic() === true) ?
            sprintf(
                'static method <info>%s::%s()</info>',
                $usage->className(),
                $usage->name()
            ) :
            sprintf(
                'method <info>%s->%s()</info>',
                $usage->className(),
                $usage->name()
            );

        $violationInfo = sprintf(
            'Calling deprecated %s',
            $message
        );

        return $violationInfo;
    }
}
