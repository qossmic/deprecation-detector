<?php

namespace SensioLabs\DeprecationDetector\Tests\Visitor\Usage;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\DeprecatedLanguageUsage;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindLanguageDeprecations;

class FindLanguageDeprecationsTest extends FindTestCase
{
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
