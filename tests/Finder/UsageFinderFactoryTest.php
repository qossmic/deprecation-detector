<?php

namespace SensioLabs\DeprecationDetector\Tests\Finder;

use SensioLabs\DeprecationDetector\Finder\UsageFinderFactory;
use Symfony\Component\Finder\Finder;

class UsageFinderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFinder()
    {
        $factory = new UsageFinderFactory();

        $this->assertInstanceOf(Finder::class, $factory->createFinder());
    }
}
