<?php

namespace SensioLabs\DeprecationDetector\Tests\Visitor\Usage;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindLanguageDeprecations;

class FindLanguageDeprecationsTest extends FindTestCase
{
    // http://php.net/manual/en/migration53.deprecated.php
    public function testAssignOfNewByReference()
    {
        $source = <<<'EOC'
<?php

$foo =& new Foo();

EOC;
        $splFileInfo = $this->prophesize('Symfony\Component\Finder\SplFileInfo');
        $phpFileInfo = $this->parsePhpFileFromStringAndTraverseWithVisitor(
            $file = PhpFileInfo::create($splFileInfo->reveal()),
            $source,
            new FindLanguageDeprecations()
        );

        $this->assertEquals(
            array(
                new DeprecatedLanguageUsage('Assigning the return value of new by reference is now deprecated.', 'Since PHP 5.3 use normal assignment instead.', 3),
            ),
            $phpFileInfo->getDeprecatedLanguageUsages()
        );
    }

    public function testAssignByReference()
    {
        $source = <<<'EOC'
<?php

$foo =& $bar;

EOC;
        $splFileInfo = $this->prophesize('Symfony\Component\Finder\SplFileInfo');
        $phpFileInfo = $this->parsePhpFileFromStringAndTraverseWithVisitor(
            $file = PhpFileInfo::create($splFileInfo->reveal()),
            $source,
            new FindLanguageDeprecations()
        );

        $this->assertEquals(
            array(),
            $phpFileInfo->getDeprecatedLanguageUsages()
        );
    }
}
