<?php

namespace SensioLabs\DeprecationDetector\Tests\FileInfo\Usage;

use SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage;

class DeprecatedLanguageUsageTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $deprecatedLanguageUsage = new DeprecatedLanguageUsage('name', 'comment', 0);

        $this->assertInstanceOf(DeprecatedLanguageUsage::class, $deprecatedLanguageUsage);
    }

    public function testGetClassName()
    {
        $deprecatedLanguageUsage = new DeprecatedLanguageUsage('name', 'comment', 0);

        $this->assertSame('name', $deprecatedLanguageUsage->name());
    }

    public function testGetLineNumber()
    {
        $deprecatedLanguageUsage = new DeprecatedLanguageUsage('name', 'comment', 0);

        $this->assertSame(0, $deprecatedLanguageUsage->getLineNumber());
    }

    public function testGetComment()
    {
        $deprecatedLanguageUsage = new DeprecatedLanguageUsage('name', 'comment', 0);

        $this->assertSame('comment', $deprecatedLanguageUsage->comment());
    }
}
