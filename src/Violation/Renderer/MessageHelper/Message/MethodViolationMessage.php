<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class MethodViolationMessage extends BaseViolationMessage
{
    const USAGE_NAME = 'SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage';

    public function __construct()
    {
        parent::__construct(self::USAGE_NAME);
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
