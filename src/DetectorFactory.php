<?php

namespace SensioLabs\DeprecationDetector;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use SensioLabs\DeprecationDetector\Configuration\Configuration;
use SensioLabs\DeprecationDetector\Console\Output\DefaultProgressOutput;
use SensioLabs\DeprecationDetector\Console\Output\VerboseProgressOutput;
use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;
use SensioLabs\DeprecationDetector\Parser\DeprecationParser;
use SensioLabs\DeprecationDetector\Parser\UsageParser;
use SensioLabs\DeprecationDetector\RuleSet\Cache;
use SensioLabs\DeprecationDetector\RuleSet\Loader\ComposerLoader;
use SensioLabs\DeprecationDetector\RuleSet\Loader\DirectoryLoader;
use SensioLabs\DeprecationDetector\RuleSet\Loader\FileLoader;
use SensioLabs\DeprecationDetector\RuleSet\Loader\LoaderInterface;
use SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser;
use SensioLabs\DeprecationDetector\TypeGuessing\AncestorResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\ConstructorResolver\ConstructorResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\ConstructorResolver\Visitor\ConstructorResolverVisitor;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\ComposedResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ArgumentResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\PropertyAssignResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ReattachStateToProperty;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ReattachStateToVariable;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\SymfonyResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\VariableAssignResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Visitor\SymbolTableVariableResolverVisitor;
use SensioLabs\DeprecationDetector\TypeGuessing\Symfony\ContainerReader;
use SensioLabs\DeprecationDetector\Violation\Renderer\ConsoleOutputRenderer;
use SensioLabs\DeprecationDetector\Violation\Renderer\Html\Renderer;
use SensioLabs\DeprecationDetector\Violation\Renderer\Html\RendererFactory;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\ClassViolationMessage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\InterfaceViolationMessage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\MethodDefinitionViolationMessage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\MethodViolationMessage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\SuperTypeViolationMessage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\ClassViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\ComposedViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\InterfaceViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\MethodDefinitionViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\MethodViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\SuperTypeViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\TypeHintViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationDetector;
use SensioLabs\DeprecationDetector\Violation\ViolationFilter\ComposedViolationFilter;
use SensioLabs\DeprecationDetector\Violation\ViolationFilter\MethodViolationFilter;
use SensioLabs\DeprecationDetector\Visitor\Deprecation\FindDeprecatedTagsVisitor;
use SensioLabs\DeprecationDetector\Visitor\StaticAnalysisVisitorInterface;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindArguments;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindClasses;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindInterfaces;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindMethodCalls;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindMethodDefinitions;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindStaticMethodCalls;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindSuperTypes;
use SensioLabs\DeprecationDetector\Visitor\ViolationVisitorInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Stopwatch\Stopwatch;

class DetectorFactory
{
    /**
     * @var SymbolTable
     */
    private $symbolTable;

    /**
     * @var AncestorResolver
     */
    private $ancestorResolver;

    /**
     * @param Configuration   $configuration
     * @param OutputInterface $output
     *
     * @return DeprecationDetector
     */
    public function create(Configuration $configuration, OutputInterface $output)
    {
        $this->symbolTable = new SymbolTable();

        $deprecationProgressOutput = new VerboseProgressOutput(
            new ProgressBar($output),
            $configuration->isVerbose(),
            'Deprecation detection'
        );
        $deprecationUsageParser = $this->getUsageParser($configuration);
        $deprecationUsageFinder = new ParsedPhpFileFinder(
            $deprecationUsageParser,
            $deprecationProgressOutput
        );
        $deprecationUsageFinder
            ->exclude('vendor')
            ->exclude('Tests')
            ->exclude('Test');

        $this->ancestorResolver = new AncestorResolver($deprecationUsageParser);

        $ruleSetProgressOutput = new VerboseProgressOutput(
            new ProgressBar($output),
            $configuration->isVerbose(),
            'RuleSet generation'
        );
        $ruleSetDeprecationParser = $this->getDeprecationParser();
        $ruleSetDeprecationFinder = new ParsedPhpFileFinder(
            $ruleSetDeprecationParser,
            $ruleSetProgressOutput
        );
        $ruleSetDeprecationFinder
            ->contains('@deprecated')
            ->exclude('vendor')
            ->exclude('Tests')
            ->exclude('Test');
        $deprecationDirectoryTraverser = new DirectoryTraverser($ruleSetDeprecationFinder);

        $violationDetector = $this->getViolationDetector($configuration);

        $renderer = $this->getRenderer($configuration, $output);

        $ruleSetLoader = $this->getRuleSetLoader($deprecationDirectoryTraverser, $configuration);

        $progressOutput = new DefaultProgressOutput($output, new Stopwatch());

        return new DeprecationDetector(
            $ruleSetLoader,
            $this->ancestorResolver,
            $deprecationUsageFinder,
            $violationDetector,
            $renderer,
            $progressOutput
        );
    }

    /**
     * Violation Visitor.
     */

    /**
     * @param Configuration $configuration
     *
     * @return UsageParser
     */
    private function getUsageParser(Configuration $configuration)
    {
        return new UsageParser(
            $this->getStaticAnalysisVisitors($configuration),
            $this->getViolationVisitors(),
            $this->getBaseTraverser(),
            new NodeTraverser(),
            new NodeTraverser()
        );
    }

    /**
     * @param Configuration $configuration
     *
     * @return StaticAnalysisVisitorInterface[]
     */
    private function getStaticAnalysisVisitors(Configuration $configuration)
    {
        return array(
            $symbolTableVariableResolverVisitor = new SymbolTableVariableResolverVisitor(
                $this->getSymbolTableVariableResolver($configuration),
                $this->symbolTable
            ),

            // the constructor resolver should be registered last
            new ConstructorResolverVisitor(
                new ConstructorResolver(
                    $this->symbolTable,
                    array(
                        $symbolTableVariableResolverVisitor,
                    )
                )
            ),
        );
    }

    /**
     * @param Configuration $configuration
     *
     * @return ComposedResolver
     */
    private function getSymbolTableVariableResolver(Configuration $configuration)
    {
        $composedResolver = new ComposedResolver();
        $composedResolver->addResolver(new ArgumentResolver($this->symbolTable));
        $composedResolver->addResolver(new ReattachStateToVariable($this->symbolTable));
        $composedResolver->addResolver(new ReattachStateToProperty($this->symbolTable));
        $composedResolver->addResolver(new VariableAssignResolver($this->symbolTable));
        $composedResolver->addResolver(new PropertyAssignResolver($this->symbolTable));

        /* @TODO: only load the container if the project is a symfony project */
        $containerReader = new ContainerReader();
        $containerReader->loadContainer($configuration->containerPath());
        $composedResolver->addResolver(new SymfonyResolver($this->symbolTable, $containerReader));

        return $composedResolver;
    }

    /**
     * @return ViolationVisitorInterface[]
     */
    private function getViolationVisitors()
    {
        return array(
            new FindInterfaces(),
            new FindArguments(),
            new FindClasses(),
            new FindSuperTypes(),
            new FindMethodCalls(),
            new FindMethodDefinitions(),
            new FindStaticMethodCalls(),
        );
    }

    /**
     * ViolationDetector.
     */

    /**
     * @param Configuration $configuration
     *
     * @return ViolationDetector
     */
    private function getViolationDetector(Configuration $configuration)
    {
        $violationChecker = $this->getViolationChecker($configuration);
        $violationFilter = $this->getViolationFilter($configuration);

        return new ViolationDetector(
            $violationChecker,
            $violationFilter
        );
    }

    /**
     * @param Configuration $configuration
     *
     * @return ComposedViolationChecker
     */
    private function getViolationChecker(Configuration $configuration)
    {
        $violationChecker = new ComposedViolationChecker(
            array(
                new ClassViolationChecker(),
                new InterfaceViolationChecker(),
                new MethodViolationChecker($this->ancestorResolver),
                new SuperTypeViolationChecker(),
                new TypeHintViolationChecker(),
                new MethodDefinitionViolationChecker($this->ancestorResolver),
            )
        );

        return $violationChecker;
    }

    /**
     * @param Configuration $configuration
     *
     * @return ComposedViolationFilter
     */
    private function getViolationFilter(Configuration $configuration)
    {
        $violationFilters = array();
        if ('' !== $configuration->filteredMethodCalls()) {
            $violationFilters[] = MethodViolationFilter::fromString($configuration->filteredMethodCalls());
        }

        return new ComposedViolationFilter($violationFilters);
    }

    /**
     * Renderer.
     */
    private function getRenderer(Configuration $configuration, $output)
    {
        $messageHelper = $this->getMessageHelper();

        if ($logFilePath = $configuration->logHtml()) {
            $factory = new RendererFactory($messageHelper, new Filesystem());
            return $factory->createHtmlOutputRenderer($logFilePath);
        }

        return new ConsoleOutputRenderer($output, $messageHelper);
    }

    /**
     * @return MessageHelper
     */
    private function getMessageHelper()
    {
        return new MessageHelper(
            array(
                new ClassViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage'),
                new InterfaceViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage'),
                new MethodViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage'),
                new MethodDefinitionViolationMessage('SensioLabs\DeprecationDetector\FileInfo\MethodDefinition'),
                new SuperTypeViolationMessage('SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage'),
            )
        );
    }

    /**
     * DeprecationParser.
     */
    private function getDeprecationParser()
    {
        return new DeprecationParser(
            array(
                new FindDeprecatedTagsVisitor(),
            ),
            $this->getBaseTraverser()
        );
    }

    /**
     * RuleSet.
     */

    /**
     * @param DirectoryTraverser $traverser
     * @param Configuration      $configuration
     *
     * @return LoaderInterface
     */
    private function getRuleSetLoader(DirectoryTraverser $traverser, Configuration $configuration)
    {
        $ruleSetCache = new Cache(new Filesystem());

        if ($configuration->useCachedRuleSet()) {
            $ruleSetCache->disable();
        } else {
            $ruleSetCache->setCacheDir($configuration->ruleSetCacheDir());
        }

        if (is_dir($configuration->ruleSet())) {
            $loader = new DirectoryLoader($traverser, $ruleSetCache);
        } elseif ('composer.lock' === basename($configuration->ruleSet())) {
            $loader = new ComposerLoader($traverser, $ruleSetCache);
        } else {
            $loader = new FileLoader();
        }

        return $loader;
    }

    /**
     * @return NodeTraverser
     */
    private function getBaseTraverser()
    {
        $baseTraverser = new NodeTraverser();
        $baseTraverser->addVisitor(new NameResolver());

        return $baseTraverser;
    }
}
