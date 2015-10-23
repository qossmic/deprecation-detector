<?php

namespace SensioLabs\DeprecationDetector\RuleSet\Loader;

class CouldNotLoadRuleSetException extends \Exception
{
    /**
     * @param string $ruleSet
     */
    public function __construct($ruleSet)
    {
        parent::__construct(
            sprintf('<error>Could not load ruleset: %s, aborting.</error>', $ruleSet)
        );
    }
}