<?php

namespace SensioLabs\DeprecationDetector\Tests\FileInfo\Deprecation;

use SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation;

class ClassDeprecationTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $classDeprecation = new ClassDeprecation('className', 'comment');

        $this->assertInstanceOf(
            ClassDeprecation::class,
            $classDeprecation
        );
    }

    public function testGetClassName()
    {
        $classDeprecation = new ClassDeprecation('className', 'comment');

        $this->assertSame('className', $classDeprecation->name());
    }

    public function testGetComment()
    {
        $classDeprecation = new ClassDeprecation('className', 'comment');

        $this->assertSame('comment', $classDeprecation->comment());
    }
}
