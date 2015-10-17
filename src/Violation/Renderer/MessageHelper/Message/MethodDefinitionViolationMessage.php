<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\MethodDefinition;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

class MethodDefinitionViolationMessage extends BaseViolationMessage implements ViolationMessageInterface
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
