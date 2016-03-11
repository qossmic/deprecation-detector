<?php

namespace SensioLabs\DeprecationDetector\Tests;

use SensioLabs\DeprecationDetector\Configuration\Configuration;
use SensioLabs\DeprecationDetector\DeprecationDetector;
use SensioLabs\DeprecationDetector\DetectorFactory;
use Symfony\Component\Console\Output\OutputInterface;

class DetectorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $factory = new DetectorFactory();

        $this->assertInstanceOf(DetectorFactory::class, $factory);
    }

    public function testCreateDetector()
    {
        $configuration = $this->prophesize(Configuration::class);
        $output = $this->prophesize(OutputInterface::class);

        $factory = new DetectorFactory();

        $this->assertInstanceOf(
            DeprecationDetector::class,
            $factory->create(
                $configuration->reveal(),
                $output->reveal()
            )
        );
    }
}
