<?php


namespace SensioLabs\DeprecationDetector\Violation\ViolationFilter;


use SensioLabs\DeprecationDetector\Violation\Violation;

interface ViolationFilterInterface
{

    /**
     * @param Violation $violation
     * @return bool
     */
    public function violationIsFiltered(Violation $violation);

}