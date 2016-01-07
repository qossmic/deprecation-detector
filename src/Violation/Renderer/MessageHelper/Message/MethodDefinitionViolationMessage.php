<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\MethodDefinition;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class MethodDefinitionViolationMessage extends BaseViolationMessage
{
    const USAGE_NAME = 'SensioLabs\DeprecationDetector\FileInfo\MethodDefinition';

    public function __construct()
    {
        parent::__construct(self::USAGE_NAME);
    }

    public function message(UsageInterface $usage)
    {
        if (!$usage instanceof MethodDefinition) {
            return '';
        }

        return sprintf(
            'Overriding deprecated method <info>%s->%s()</info>',
            $usage->parentName(),
            $usage->name()
        );
    }
}
