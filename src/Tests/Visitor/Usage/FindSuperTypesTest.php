<?php

namespace SensioLabs\DeprecationDetector\Tests\Visitor\Usage;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindSuperTypes;

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

        $splFileInfo = $this->prophesize('Symfony\Component\Finder\SplFileInfo');
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

        $splFileInfo = $this->prophesize('Symfony\Component\Finder\SplFileInfo');
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
}
