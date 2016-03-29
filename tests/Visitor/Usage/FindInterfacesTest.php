<?php

namespace SensioLabs\DeprecationDetector\Tests\Visitor\Usage;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use Prophecy\Argument;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindInterfaces;
use Symfony\Component\Finder\SplFileInfo;

class FindInterfacesTest extends FindTestCase
{
    public function testClassWithInterface()
    {
        $source = <<<EOC
<?php
namespace Foo;

class Bar implements Baz
{
}
EOC;
        $splFileInfo = $this->prophesize(SplFileInfo::class);
        $phpFileInfo = $this->parsePhpFileFromStringAndTraverseWithVisitor(
            $file = PhpFileInfo::create($splFileInfo->reveal()),
            $source,
            new FindInterfaces()
        );

        $this->assertEquals(
            ['Foo\Bar' => [new InterfaceUsage('Foo\Baz', 'Foo\Bar', 4)]],
            $phpFileInfo->interfaceUsages()
        );
    }

    public function testClassWithoutInterface()
    {
        $source = <<<EOC
<?php
namespace Foo;

class Bar
{
}
EOC;
        $splFileInfo = $this->prophesize(SplFileInfo::class);
        $phpFileInfo = $this->parsePhpFileFromStringAndTraverseWithVisitor(
            $file = PhpFileInfo::create($splFileInfo->reveal()),
            $source,
            new FindInterfaces()
        );

        $this->assertEquals(
            [],
            $phpFileInfo->interfaceUsages()
        );
    }

    public function testSkipsAnonymousClasses()
    {
        $phpFileInfo = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');
        $phpFileInfo->addInterfaceUsage(Argument::any())->shouldNotBeCalled();

        $visitor = new FindInterfaces();

        $node = new Class_(
            null,
            ['implements' => [new Name('SomeInterface')]]
        );

        $visitor->enterNode($node);
    }
}
