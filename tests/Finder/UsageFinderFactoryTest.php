<?php

namespace SensioLabs\DeprecationDetector\Tests\Finder;

use SensioLabs\DeprecationDetector\Finder\UsageFinderFactory;

class UsageFinderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFinder()
    {
        $factory = new UsageFinderFactory();

        $this->assertInstanceOf('Symfony\Component\Finder\Finder', $factory->createFinder());
    }
}
