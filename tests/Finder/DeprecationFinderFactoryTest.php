<?php

namespace SensioLabs\DeprecationDetector\Tests\Finder;

use SensioLabs\DeprecationDetector\Finder\DeprecationFinderFactory;
use Symfony\Component\Finder\Finder;

class DeprecationFinderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFinder()
    {
        $factory = new DeprecationFinderFactory();

        $this->assertInstanceOf(Finder::class, $factory->createFinder());
    }
}
