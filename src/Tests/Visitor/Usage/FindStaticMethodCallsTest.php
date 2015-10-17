<?php

namespace SensioLabs\DeprecationDetector\Tests\Visitor\ViolationVisitor;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage;
use SensioLabs\DeprecationDetector\Tests\Visitor\Usage\FindTestCase;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindStaticMethodCalls;

class FindStaticMethodCallsTest extends FindTestCase
{
    public function testClassIsInitializable()
    {
        $visitor = new FindStaticMethodCalls();
        $this->assertInstanceOf('SensioLabs\DeprecationDetector\Visitor\Usage\FindStaticMethodCalls', $visitor);
    }

    public function testNoNewStatement()
    {
        $source = <<<'EOC'
<?php

$bar = Bar::bazinga();
Logger::log('hello world');

EOC;

        $splFileInfo = $this->prophesize('Symfony\Component\Finder\SplFileInfo');
        $phpFileInfo = $this->parsePhpFileFromStringAndTraverseWithVisitor(
            $file = PhpFileInfo::create($splFileInfo->reveal()),
            $source,
            new FindStaticMethodCalls()
        );

        $usages = $phpFileInfo->methodUsages();
        $this->assertEquals(
            new MethodUsage('bazinga', 'Bar', 3, true),
            $usages[0]
        );
        $this->assertEquals(
            new MethodUsage('log', 'Logger', 4, true),
            $usages[1]
        );
    }
}
