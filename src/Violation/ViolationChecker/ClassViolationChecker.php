<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;

class ClassViolationChecker implements ViolationCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function check(PhpFileInfo $phpFileInfo, RuleSet $ruleSet)
    {
        $violations = array();

        foreach ($phpFileInfo->classUsages() as $classUsage) {
            if ($ruleSet->hasClass($classUsage->name())) {
                $violations[] = new Violation(
                    $classUsage,
                    $phpFileInfo,
                    $ruleSet->getClass($classUsage->name())->comment()
                );
            }
        }

        return $violations;
    }
}
