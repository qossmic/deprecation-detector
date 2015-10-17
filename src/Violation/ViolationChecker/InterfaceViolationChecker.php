<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;

class InterfaceViolationChecker implements ViolationCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function check(PhpFileInfo $phpFileInfo, RuleSet $ruleSet)
    {
        $violations = array();

        foreach ($phpFileInfo->interfaceUsages() as $interfaceUsageGroup) {
            foreach ($interfaceUsageGroup as $interfaceUsage) {
                if ($ruleSet->hasInterface($interfaceUsage->name())) {
                    $violations[] = new Violation(
                        $interfaceUsage,
                        $phpFileInfo,
                        $ruleSet->getInterface($interfaceUsage->name())->comment()
                    );
                }
            }
        }

        return $violations;
    }
}
