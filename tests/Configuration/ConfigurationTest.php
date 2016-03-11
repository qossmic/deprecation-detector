<?php

namespace SensioLabs\DeprecationDetector\Tests\Configuration;

use SensioLabs\DeprecationDetector\Configuration\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $configuration = new Configuration(
            'path/to/rule_set',
            'path/to/container.xml',
            true,
            'cacheDir',
            '',
            true,
            true,
            'html.log',
            false
        );

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Configuration\Configuration', $configuration);
    }

    public function testRuleSet()
    {
        $configuration = new Configuration(
            'path/to/rule_set',
            'path/to/container.xml',
            true,
            'path/to/cache/dir/',
            '',
            true,
            true,
            'html.log',
            false
        );

        $this->assertEquals('path/to/rule_set', $configuration->ruleSet());
        $this->assertEquals('path/to/container.xml', $configuration->containerPath());
        $this->assertTrue($configuration->useCachedRuleSet());
        $this->assertEquals('path/to/cache/dir/', $configuration->ruleSetCacheDir());
        $this->assertEquals('', $configuration->filteredMethodCalls());
        $this->assertTrue($configuration->failOnDeprecation());
        $this->assertTrue($configuration->isVerbose());
        $this->assertEquals('html.log', $configuration->logHtml());
    }
}
