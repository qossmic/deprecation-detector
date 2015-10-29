<?php

namespace SensioLabs\DeprecationDetector\tests;

use SensioLabs\DeprecationDetector\DetectorFactory;

class DetectorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $factory = new DetectorFactory();

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\DetectorFactory', $factory);
    }

    public function testCreateDetector()
    {
        $configuration = $this->prophesize('SensioLabs\DeprecationDetector\Configuration\Configuration');
        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');

        $factory = new DetectorFactory();

        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\DeprecationDetector',
            $factory->create(
                $configuration->reveal(),
                $output->reveal()
            )
        );
    }
}
