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
        foreach ($this->violationFilters as $violationFilter) {
            if (true === $violationFilter->violationIsFiltered($violation)) {
                return true;
            }
        }

        return false;
    }
}
