<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;

class LanguageViolationChecker implements ViolationCheckerInterface
{
    /**
     * {@inheritDoc}
     */
    public function check(PhpFileInfo $phpFileInfo, RuleSet $ruleSet)
    {
        $violations = array();

        foreach ($phpFileInfo->getDeprecatedLanguageUsages() as $deprecatedLanguageUsage) {
            $violations[] = new Violation(
                $deprecatedLanguageUsage,
                $phpFileInfo,
                $deprecatedLanguageUsage->comment()
            );
        }

        return $violations;
    }
}
