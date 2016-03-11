<?php

namespace SensioLabs\DeprecationDetector\Tests\Visitor\Usage;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use Prophecy\Argument;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindSuperTypes;
use Symfony\Component\Finder\SplFileInfo;

class FindSuperTypesTest extends FindTestCase
{
    public function testTypeClassExists()
    {
        $source = <<<EOC
<?php
namespace Foo;

class Bar extends Baz {

}
EOC;

        $splFileInfo = $this->prophesize(SplFileInfo::class);
        $phpFileInfo = $this->parsePhpFileFromStringAndTraverseWithVisitor(
            $file = PhpFileInfo::create($splFileInfo->reveal()),
            $source,
            new FindSuperTypes()
        );

        $this->assertEquals(
            array('Foo\Bar' => new SuperTypeUsage('Foo\Baz', 'Foo\Bar', 4)),
            $phpFileInfo->superTypeUsages()
        );
    }

    public function testNoSuperType()
    {
        $source = <<<EOC
<?php
namespace Foo;

class Bar {

}
EOC;

        $splFileInfo = $this->prophesize(SplFileInfo::class);
        $phpFileInfo = $this->parsePhpFileFromStringAndTraverseWithVisitor(
            $file = PhpFileInfo::create($splFileInfo->reveal()),
            $source,
            new FindSuperTypes()
        );

        $this->assertEquals(
            array(),
            $phpFileInfo->superTypeUsages()
        );
    }

    public function testSkipsAnonymousClasses()
    {
        $phpFileInfo = $this->prophesize('SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo');
        $phpFileInfo->addSuperTypeUsage(Argument::any())->shouldNotBeCalled();

        $visitor = new FindSuperTypes();

        $node = new Class_(
            null,
            array('extends' => new Name('SomeInterface'))
        );

        $visitor->enterNode($node);
    }
}
