<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;

interface ViolationCheckerInterface
{
    /**
     * @param PhpFileInfo $phpFileInfo
     * @param RuleSet     $ruleSet
     *
     * @return \SensioLabs\DeprecationDetector\Violation\Violation[]
     */
    public function check(PhpFileInfo $phpFileInfo, RuleSet $ruleSet);
}
