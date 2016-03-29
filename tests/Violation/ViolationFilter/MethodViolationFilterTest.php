<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationFilter;

use SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage;
use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;
use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationFilter\MethodViolationFilter;

class MethodViolationFilterTest extends \PHPUnit_Framework_TestCase
{
    private $filterList = ['Checked::method', '\\Foo\\Checked::method2'];

    /**
     * @var MethodViolationFilter
     */
    private $methodViolationFilter = null;

    public function setUp()
    {
        parent::setUp();
        $this->methodViolationFilter = new MethodViolationFilter($this->filterList);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->methodViolationFilter = null;
    }

    /**
     * @dataProvider getFilteredViolations
     */
    public function testFilterViolations($checkedClass, $checkedName, $expectedToBeFiltered)
    {
        $usage = $this->prophesize(MethodUsage::class);
        $usage->className()->willReturn($checkedClass);
        $usage->name()->willReturn($checkedName);
        $violation = $this->prophesize(Violation::class);
        $violation->getUsage()->willReturn($usage->reveal());
        $filtered = $this->methodViolationFilter->isViolationFiltered($violation->reveal());
        $this->assertEquals($expectedToBeFiltered, $filtered);
    }

    /**
     * @dataProvider getFilteredViolations
     */
    public function testNotFilteringNonMethodUsages($checkedClass, $checkedName, $expectedToBeFiltered)
    {
        $usage = $this->prophesize(UsageInterface::class);
        $violation = $this->prophesize(Violation::class);
        $violation->getUsage()->willReturn($usage->reveal());
        $filtered = $this->methodViolationFilter->isViolationFiltered($violation->reveal());
        $this->assertFalse($filtered);
    }

    public function getFilteredViolations()
    {
        return [
            ['Checked', 'method', true],
            ['\\Foo\\Checked', 'method2', true],
            ['Checkedd', 'method', false],
            ['\\Foo\\Checked\\Bar', 'method2', false],

        ];
    }
}
