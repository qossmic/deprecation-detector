<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationFilter;

use SensioLabs\DeprecationDetector\FileInfo\MethodDefinition;
use SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage;
use SensioLabs\DeprecationDetector\Violation\Violation;

class MethodViolationFilter implements ViolationFilterInterface
{
    /**
     * @var array
     */
    private $filterArray;

    public function __construct(array $filterArray)
    {
        $this->filterArray = $filterArray;
    }

    /**
     * @param string $filterConfig
     *
     * @return MethodViolationFilter
     */
    public static function fromString($filterConfig)
    {
        return new static(explode(',', $filterConfig));
    }

    /**
     * {@inheritdoc}
     */
    public function isViolationFiltered(Violation $violation)
    {
        $usage = $violation->getUsage();
        if (!$usage instanceof MethodUsage && !$usage instanceof MethodDefinition) {
            return false;
        }

        if ($usage instanceof MethodUsage) {
            $className = $usage->className();
        } elseif ($usage instanceof MethodDefinition) {
            $className = $usage->parentName();
        }

        $method = $usage->name();
        $usageString = sprintf('%s::%s', $className, $method);

        return in_array($usageString, $this->filterArray);
    }
}
