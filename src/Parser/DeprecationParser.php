<?php

namespace SensioLabs\DeprecationDetector\Parser;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\Visitor\DeprecationVisitorInterface;

class DeprecationParser extends Parser implements ParserInterface
{
    /**
     * @var NodeTraverser
     */
    protected $traverser;

    /**
     * @var DeprecationVisitorInterface[]
     */
    protected $deprecationVisitors = [];

    /**
     * @param DeprecationVisitorInterface[] $visitors
     * @param NodeTraverser                 $baseTraverser
     */
    public function __construct(array $visitors, NodeTraverser $baseTraverser)
    {
        parent::__construct(new Lexer());
        $this->traverser = $baseTraverser;

        array_map([$this, 'addDeprecationVisitor'], $visitors);
    }

    /**
     * @param DeprecationVisitorInterface $visitor
     */
    public function addDeprecationVisitor(DeprecationVisitorInterface $visitor)
    {
        $this->deprecationVisitors[] = $visitor;
        $this->traverser->addVisitor($visitor);
    }

    /**
     * @param PhpFileInfo $phpFileInfo
     *
     * @return PhpFileInfo
     */
    public function parseFile(PhpFileInfo $phpFileInfo)
    {
        foreach ($this->deprecationVisitors as $visitor) {
            $visitor->setPhpFileInfo($phpFileInfo);
        }

        $this->traverser->traverse($this->parse($phpFileInfo->getContents()));

        return $phpFileInfo;
    }
}
