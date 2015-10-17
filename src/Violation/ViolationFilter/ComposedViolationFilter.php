<?php

namespace SensioLabs\DeprecationDetector\Violation\ViolationFilter;

use SensioLabs\DeprecationDetector\Violation\Violation;

class ComposedViolationFilter implements ViolationFilterInterface
{
    /**
     * @var ViolationFilterInterface[]
     */
    private $violationFilters;

    /**
     * @param ViolationFilterInterface $violationFilters
     */
    public function __construct(array $violationFilters)
    {
        $this->violationFilters = $violationFilters;
    }

    /**
     * @param Violation $violation
     *
     * @return bool
     */
    public function violationIsFiltered(Violation $violation)
    {
        $isFiltered = false;
        foreach ($this->violationFilters as $violationFilter) {
            $isFiltered = $isFiltered || $violationFilter->violationIsFiltered($violation);
            if (true === $isFiltered) {
                return true;
            }
        }

        return $isFiltered;
    }
}
