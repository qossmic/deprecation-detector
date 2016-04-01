<?php

namespace SensioLabs\DeprecationDetector\Tests\TypeGuessing\Symfony;

use SensioLabs\DeprecationDetector\TypeGuessing\Symfony\ContainerReader;

class ContainerReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $container = new ContainerReader();

        $this->assertInstanceOf(ContainerReader::class, $container);
    }

    public function testWithoutLoading()
    {
        $container = new ContainerReader();

        $this->assertNull($container->has('service.unit'));
        $this->assertNull($container->get('service.unit'));
    }

    public function testLoadInvalidPath()
    {
        $container = new ContainerReader();
        $loaded = $container->loadContainer(__DIR__.'/invalidPath.xml');

        $this->assertFalse($loaded);
    }

    public function testLoadInvalidDump()
    {
        $container = new ContainerReader();
        $loaded = $container->loadContainer(__FILE__);

        $this->assertFalse($loaded);
    }

    public function testLoadValidDump()
    {
        $container = new ContainerReader();
        $loaded = $container->loadContainer(__DIR__.'/containerDump.xml');

        $this->assertTrue($loaded);

        $this->assertTrue($container->has('service.unit'));
        $this->assertFalse($container->has('service.invalid'));

        $this->assertEquals('UnitTest\Class', $container->get('service.unit'));
        $this->assertNull($container->get('service.unit2'));
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testInvalidCall()
    {
        $container = new ContainerReader();
        $container->loadContainer(__DIR__.'/containerDump.xml');

        $container->getDefinition('service.unit');
    }
}
