<?php

namespace SensioLabs\DeprecationDetector\Tests\Violation\ViolationFilter;

use SensioLabs\DeprecationDetector\Violation\ViolationFilter\MethodViolationFilter;

class MethodViolationFilterTest extends \PHPUnit_Framework_TestCase
{
    private $filterList = array('Checked::method', '\\Foo\\Checked::method2');

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
        $usage = $this->prophesize('\SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage');
        $usage->className()->willReturn($checkedClass);
        $usage->name()->willReturn($checkedName);
        $violation = $this->prophesize('\SensioLabs\DeprecationDetector\Violation\Violation');
        $violation->getUsage()->willReturn($usage->reveal());
        $filtered = $this->methodViolationFilter->isViolationFiltered($violation->reveal());
        $this->assertEquals($expectedToBeFiltered, $filtered);
    }

    /**
     * @dataProvider getFilteredViolations
     */
    public function testNotFilteringNonMethodUsages($checkedClass, $checkedName, $expectedToBeFiltered)
    {
        $usage = $this->prophesize('\SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface');
        $violation = $this->prophesize('\SensioLabs\DeprecationDetector\Violation\Violation');
        $violation->getUsage()->willReturn($usage->reveal());
        $filtered = $this->methodViolationFilter->isViolationFiltered($violation->reveal());
        $this->assertFalse($filtered);
    }

    public function getFilteredViolations()
    {
        return array(
            array('Checked', 'method', true),
            array('\\Foo\\Checked', 'method2', true),
            array('Checkedd', 'method', false),
            array('\\Foo\\Checked\\Bar', 'method2', false),

        );
    }
}
