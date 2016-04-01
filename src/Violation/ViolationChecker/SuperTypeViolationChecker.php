<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;

class SuperTypeViolationChecker implements ViolationCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function check(PhpFileInfo $phpFileInfo, RuleSet $ruleSet)
    {
        $violations = [];

        foreach ($phpFileInfo->superTypeUsages() as $superTypeUsage) {
            if ($ruleSet->hasClass($superTypeUsage->name())) {
                $violations[] = new Violation(
                    $superTypeUsage,
                    $phpFileInfo,
                    $ruleSet->getClass($superTypeUsage->name())->comment()
                );
            }
        }

        return $violations;
    }
}
