<?php

namespace SensioLabs\DeprecationDetector\Tests\Visitor\Usage;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindInterfaces;

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
        $splFileInfo = $this->prophesize('Symfony\Component\Finder\SplFileInfo');
        $phpFileInfo = $this->parsePhpFileFromStringAndTraverseWithVisitor(
            $file = PhpFileInfo::create($splFileInfo->reveal()),
            $source,
            new FindInterfaces()
        );

        $this->assertEquals(
            array('Foo\Bar' => array(new InterfaceUsage('Foo\Baz', 'Foo\Bar', 4))),
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
        $splFileInfo = $this->prophesize('Symfony\Component\Finder\SplFileInfo');
        $phpFileInfo = $this->parsePhpFileFromStringAndTraverseWithVisitor(
            $file = PhpFileInfo::create($splFileInfo->reveal()),
            $source,
            new FindInterfaces()
        );

        $this->assertEquals(
            array(),
            $phpFileInfo->interfaceUsages()
        );
    }
}
