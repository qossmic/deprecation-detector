<?php

namespace SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\Exception;

class ComposerFileIsInvalidException extends ComposerException
{
    public function __construct($lockPath)
    {
        parent::__construct(
            sprintf('composer.lock file "%s" is invalid.', $lockPath)
        );
    }
}
