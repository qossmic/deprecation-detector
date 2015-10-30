<?php

namespace SensioLabs\DeprecationDetector\RuleSet\Loader;

class CouldNotLoadRuleSetException extends \Exception
{
    /**
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
