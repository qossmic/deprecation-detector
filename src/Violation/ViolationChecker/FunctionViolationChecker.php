<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;

class FunctionViolationChecker implements ViolationCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function check(PhpFileInfo $phpFileInfo, RuleSet $ruleSet)
    {
        $violations = [];

        foreach ($phpFileInfo->getFunctionUsages() as $functionUsage) {
            if ($ruleSet->hasFunction($functionUsage->name())) {
                $violations[] = new Violation(
                    $functionUsage,
                    $phpFileInfo,
                    $ruleSet->getFunction($functionUsage->name())->comment()
                );
            }
        }

        return $violations;
    }
}
