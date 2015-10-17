<?php

namespace SensioLabs\DeprecationDetector\Tests\Visitor\Usage;

use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\Visitor\VisitorInterface;
use PhpParser\Parser;

class FindTestCase extends \PHPUnit_Framework_TestCase
{
    protected function parsePhpFileFromStringAndTraverseWithVisitor(
        PhpFileInfo $phpFileInfo,
        $source,
        VisitorInterface $visitor
    ) {
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor($visitor->setPhpFileInfo($phpFileInfo));
        $parser = new Parser(new Emulative());
        $traverser->traverse($parser->parse($source));

        return $phpFileInfo;
    }
}
