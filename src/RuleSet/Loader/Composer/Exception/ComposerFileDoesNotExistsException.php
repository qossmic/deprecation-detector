<?php

namespace SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Exception;

class ComposerFileDoesNotExistsException extends ComposerException
{
    public function __construct($lockPath)
    {
        parent::__construct(
            sprintf('composer.lock file "%s" does not exist', $lockPath)
        );
    }
}
