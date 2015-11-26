<?php

namespace SensioLabs\DeprecationDetector\Tests\TypeGuessing\SymbolTable\Visitor;

use PhpParser\Lexer\Emulative;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use Prophecy\Argument;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\PropertyAssignResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ReattachStateToProperty;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ReattachStateToVariable;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\VariableAssignResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Visitor\SymbolTableVariableResolverVisitor;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindMethodCalls;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\ComposedResolver;

class SymbolTableVariableResolverVisitorTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $visitor = new SymbolTableVariableResolverVisitor(new ComposedResolver(), new SymbolTable());
        $this->assertInstanceOf(
            'SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Visitor\SymbolTableVariableResolverVisitor',
            $visitor
        );
    }

    public function testSkipsAnonymousClasses()
    {
        $symbolTable = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable');
        $symbolTable->enterScope(Argument::any())->shouldNotBeCalled();
        $symbolTable->setSymbol(Argument::any())->shouldNotBeCalled();
        $symbolTable->leaveScope()->shouldNotBeCalled();

        $resolver = $this->prophesize('SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\ComposedResolver');
        $visitor = new SymbolTableVariableResolverVisitor($resolver->reveal(), $symbolTable->reveal());

        $node = new Class_(null);

        $visitor->enterNode($node);
    }

    public function testVariableResolver()
    {
        $source = <<<'EOC'
<?php
namespace Foo;
class Bar
{
    function a() {
    }
    function b() {
        $this->a();
        $x = new Bar1();
        $x->a();
        $x->b();
        $c = new Bar2();
        $y = $c;
        $f = $y;
        $f->a();
    }
}
EOC;
        $fileInfo = PhpFileInfo::create($this->prophesize('Symfony\Component\Finder\SplFileInfo')->reveal());
        $contents = $this->traverseSourceAndReturnContents($source, $fileInfo);
        $methodUsages = $contents->methodUsages();

        $this->assertCount(4, $methodUsages);
        $this->assertEquals(
            new MethodUsage('a', 'Foo\Bar', 8, false),
            $methodUsages[0]
        );
        $this->assertEquals(
            new MethodUsage('a', 'Bar1', 10, false),
            $methodUsages[1]
        );
        $this->assertEquals(
            new MethodUsage('b', 'Bar1', 11, false),
            $methodUsages[2]
        );
        $this->assertEquals(
            new MethodUsage('a', 'Bar2', 15, false),
            $methodUsages[3]
        );
    }

    /**
     * @param string      $source
     * @param PhpFileInfo $phpFileInfo
     *
     * @return PhpFileInfo
     */
    private function traverseSourceAndReturnContents($source, PhpFileInfo $phpFileInfo)
    {
        $parser = new Parser(new Emulative());
        $nodes = $parser->parse($source);

        $staticAnalysisTraverser = new NodeTraverser();
        $staticAnalysisTraverser->addVisitor(new NameResolver());

        $table = new SymbolTable();
        $resolver = new ComposedResolver();
        $resolver->addResolver(new ReattachStateToVariable($table));
        $resolver->addResolver(new ReattachStateToProperty($table));
        $resolver->addResolver(new PropertyAssignResolver($table));
        $resolver->addResolver(new VariableAssignResolver($table));

        $resolverVisitor = new SymbolTableVariableResolverVisitor($resolver, $table);

        $staticAnalysisTraverser->addVisitor($resolverVisitor);

        $nodes = $staticAnalysisTraverser->traverse($nodes);

        $deprecationTraverser = new NodeTraverser();
        $visitor = new FindMethodCalls();
        $deprecationTraverser->addVisitor($visitor->setPhpFileInfo($phpFileInfo));
        $deprecationTraverser->traverse($nodes);

        return $phpFileInfo;
    }
}
