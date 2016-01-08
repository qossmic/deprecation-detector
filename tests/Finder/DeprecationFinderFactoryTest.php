<?php

namespace SensioLabs\DeprecationDetector\Tests\Finder;

use SensioLabs\DeprecationDetector\Finder\DeprecationFinderFactory;

class DeprecationFinderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFinder()
    {
        $factory = new DeprecationFinderFactory();

        $this->assertInstanceOf('Symfony\Component\Finder\Finder', $factory->createFinder());
    }
}
