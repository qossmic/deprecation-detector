<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationChecker;

use SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\Violation\Violation;

class TypeHintViolationChecker implements ViolationCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function check(PhpFileInfo $phpFileInfo, RuleSet $ruleSet)
    {
        $violations = array();

        foreach ($phpFileInfo->typeHintUsages() as $typeHintUsage) {
            $isClass = $ruleSet->hasClass($typeHintUsage->name());

            if ($isClass || $ruleSet->hasInterface($typeHintUsage->name())) {
                $usage = $isClass ?
                    new ClassUsage($typeHintUsage->name(), $typeHintUsage->getLineNumber()) :
                    new InterfaceUsage($typeHintUsage->name(), '', $typeHintUsage->getLineNumber());

                $comment = $isClass ?
                    $ruleSet->getClass($typeHintUsage->name())->comment() :
                    $ruleSet->getInterface($typeHintUsage->name())->comment();

                $violations[] = new Violation(
                    $usage,
                    $phpFileInfo,
                    $comment
                );
            }
        }

        return $violations;
    }
}
