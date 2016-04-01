<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationFilter;

use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationFilter\ComposedViolationFilter;
use SensioLabs\DeprecationDetector\Violation\ViolationFilter\ViolationFilterInterface;

class ComposedViolationFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $composedViolationFilter = new ComposedViolationFilter([]);

        $this->assertInstanceOf(ComposedViolationFilter::class, $composedViolationFilter);
    }

    public function testFilterNoFilters()
    {
        $composedViolationFilter = new ComposedViolationFilter([]);
        $violation = $this->prophesize(Violation::class);
        $this->assertFalse($composedViolationFilter->isViolationFiltered($violation->reveal()));
    }

    public function testFilterOneFiltering()
    {
        $violation = $this->prophesize(Violation::class);
        $violationFilter = $this->prophesize(ViolationFilterInterface::class);
        $violationFilter->isViolationFiltered($violation->reveal())->willReturn(true);
        $composedViolationFilter = new ComposedViolationFilter([$violationFilter->reveal()]);
        $this->assertTrue($composedViolationFilter->isViolationFiltered($violation->reveal()));
    }

    public function testFilterOneNotFiltering()
    {
        $violation = $this->prophesize(Violation::class);
        $violationFilter = $this->prophesize(ViolationFilterInterface::class);
        $violationFilter->isViolationFiltered($violation->reveal())->willReturn(false);
        $composedViolationFilter = new ComposedViolationFilter([$violationFilter->reveal()]);
        $this->assertFalse($composedViolationFilter->isViolationFiltered($violation->reveal()));
    }

    public function testFilterOneNotFilteringAndOneFiltering()
    {
        $violation = $this->prophesize(Violation::class);
        $violationFilter1 = $this->prophesize(ViolationFilterInterface::class);
        $violationFilter1->isViolationFiltered($violation->reveal())->willReturn(false);
        $violationFilter2 = $this->prophesize(ViolationFilterInterface::class);
        $violationFilter2->isViolationFiltered($violation->reveal())->willReturn(true);
        $composedViolationFilter = new ComposedViolationFilter([$violationFilter1->reveal(), $violationFilter2->reveal()]);
        $this->assertTrue($composedViolationFilter->isViolationFiltered($violation->reveal()));
    }
}
