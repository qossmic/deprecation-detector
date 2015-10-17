<?php

namespace SensioLabs\DeprecationDetector\Console;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use Pimple\Container;
use SensioLabs\DeprecationDetector\AncestorResolver;
use SensioLabs\DeprecationDetector\Console\Command\CheckCommand;
use SensioLabs\DeprecationDetector\Detector\ViolationDetector;
use SensioLabs\DeprecationDetector\TypeGuessing\ConstructorResolver\Visitor\ConstructorResolverVisitor;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ReattachStateToProperty;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ReattachStateToVariable;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\PropertyAssignResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\ArgumentResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\SymfonyResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Resolver\VariableAssignResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\SymbolTable;
use SensioLabs\DeprecationDetector\TypeGuessing\ConstructorResolver\ConstructorResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\ComposedResolver;
use SensioLabs\DeprecationDetector\TypeGuessing\SymbolTable\Visitor\SymbolTableVariableResolverVisitor;
use SensioLabs\DeprecationDetector\TypeGuessing\Symfony\ContainerReader;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\ClassViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\ComposedViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\InterfaceViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\MethodDefinitionViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\MethodViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\SuperTypeViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\TypeHintViolationChecker;
use SensioLabs\DeprecationDetector\Visitor\Deprecation\FindDeprecatedTagsVisitor;
use SensioLabs\DeprecationDetector\EventListener\CommandListener;
use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;
use SensioLabs\DeprecationDetector\Parser\DeprecationParser;
use SensioLabs\DeprecationDetector\Parser\UsageParser;
use SensioLabs\DeprecationDetector\RuleSet\Cache;
use SensioLabs\DeprecationDetector\RuleSet\Loader\ComposerLoader;
use SensioLabs\DeprecationDetector\RuleSet\Loader\DirectoryLoader;
use SensioLabs\DeprecationDetector\RuleSet\Loader\FileLoader;
use SensioLabs\DeprecationDetector\RuleSet\Traverser;
use SensioLabs\DeprecationDetector\Violation\Renderer;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindMethodDefinitions;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindArguments;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindClasses;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindInterfaces;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindMethodCalls;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindStaticMethodCalls;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindSuperTypes;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;

class Application extends BaseApplication
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct()
    {
        parent::__construct('SensioLabs Deprecation Detector', 'dev');

        $this->container = new Container();
        $this->buildContainer();

        $checkCommand = new CheckCommand('check');
        $this->setDispatcher($this->container['event_dispatcher']);
        $this->add($checkCommand);
        $this->setDefaultCommand('check');
    }

    public function buildContainer()
    {
        $c = $this->container;

        // EVENT DISPATCHER
        $c['event_dispatcher'] = function () {
            $dispatcher = new EventDispatcher();
            $dispatcher->addSubscriber(new CommandListener());

            return $dispatcher;
        };

        // RULESET CACHE
        $c['ruleset.cache'] = function () {
            return new Cache(new Filesystem());
        };

        // RULESET TRAVERSER
        // TODO: fix container injection
        $c['ruleset.traverser'] = $c->factory(function ($c) {
            return new Traverser($c, $c['event_dispatcher']);
        });

        // RULESET LOADER
        $c['ruleset.loader.directory'] = function ($c) {
            return new DirectoryLoader($c['ruleset.traverser'], $c['ruleset.cache']);
        };
        $c['ruleset.loader.composer'] = function ($c) {
            return new ComposerLoader($c['ruleset.traverser'], $c['ruleset.cache'], $c['event_dispatcher']);
        };
        $c['ruleset.loader.ruleset'] = function ($c) {
            return new FileLoader($c['event_dispatcher']);
        };

        $c['symboltable'] = function () {
            return new SymbolTable();
        };

        /*
         * type guesser
         */

        // SymbolTableVariableResolver
        $c['typeguesser.symboltable_variable_resolver.argument_resolver'] = function ($c) {
            return new ArgumentResolver($c['symboltable']);
        };

        $c['typeguesser.symboltable_variable_resolver.reattach_variable_state'] = function ($c) {
            return new ReattachStateToVariable($c['symboltable']);
        };

        $c['typeguesser.symboltable_variable_resolver.reattach_property_state'] = function ($c) {
            return new ReattachStateToProperty($c['symboltable']);
        };

        $c['typeguesser.symboltable_variable_resolver.variable_assign'] = function ($c) {
            return new VariableAssignResolver($c['symboltable']);
        };

        $c['typeguesser.symboltable_variable_resolver.property_assign'] = function ($c) {
            return new PropertyAssignResolver($c['symboltable']);
        };

        $c['typeguesser.symboltable_variable_resolver.symfony_resolver'] = function ($c) {
            return new SymfonyResolver($c['symboltable'], $c['symfony_container_reader']);
        };

        $c['typeguesser.symboltable_variable_resolver'] = function ($c) {
            $resolver = new ComposedResolver();

            $resolver->addResolver($c['typeguesser.symboltable_variable_resolver.argument_resolver']);
            $resolver->addResolver($c['typeguesser.symboltable_variable_resolver.reattach_variable_state']);
            $resolver->addResolver($c['typeguesser.symboltable_variable_resolver.reattach_property_state']);
            $resolver->addResolver($c['typeguesser.symboltable_variable_resolver.variable_assign']);
            $resolver->addResolver($c['typeguesser.symboltable_variable_resolver.property_assign']);
            $resolver->addResolver($c['typeguesser.symboltable_variable_resolver.symfony_resolver']);

            return $resolver;
        };

        // ConstructorResolver
        $c['typeguesser.constructor_resolver'] = function ($c) {
            $resolver = new ConstructorResolver($c['symboltable']);
            $resolver->addVisitor($c['parser.usage.visitors.symboltable_variable_resolver_visitor']);

            return $resolver;
        };

        // SymfonyContainerReader
        $c['symfony_container_reader'] = function () {
            return new ContainerReader();
        };

        /*
         * AnalysisVisitors
         */

        $c['parser.usage.visitors.constructor_resolver_visitor'] = function ($c) {
            return new ConstructorResolverVisitor($c['typeguesser.constructor_resolver']);
        };

        $c['parser.usage.visitors.symboltable_variable_resolver_visitor'] = function ($c) {
            return new SymbolTableVariableResolverVisitor(
                $c['typeguesser.symboltable_variable_resolver'],
                $c['symboltable']
            );
        };

        // PARSER

        $c['parser.base_traverser'] = function () {
            $traverser = new NodeTraverser();
            $traverser->addVisitor(new NameResolver());

            return $traverser;
        };

        $c['parser.usage'] = function ($c) {
            return new UsageParser(
                array(
                    $c['parser.usage.visitors.symboltable_variable_resolver_visitor'],
                    $c['parser.usage.visitors.constructor_resolver_visitor'], // must be registered last
                ),
                array(
                    new FindInterfaces(),
                    new FindArguments(),
                    new FindClasses(),
                    new FindSuperTypes(),
                    new FindMethodCalls(),
                    new FindMethodDefinitions(),
                    new FindStaticMethodCalls(),
                ),
                $c['parser.base_traverser'],
                new NodeTraverser(),
                new NodeTraverser()
            );
        };
        $c['parser.deprecation'] = function ($c) {

            return new DeprecationParser(
                array(
                    new FindDeprecatedTagsVisitor(),
                ),
                $c['parser.base_traverser']
            );
        };

        // FINDER
        $c['finder.php_usage'] = $c->factory(function ($c) {
            $finder = new ParsedPhpFileFinder();
            $finder
                ->exclude('vendor')
                ->exclude('Tests')
                ->exclude('Test')
                ->setParser($c['parser.usage'])
            ;

            return $finder;
        });
        $c['finder.php_deprecation'] = $c->factory(function ($c) {
            $finder = new ParsedPhpFileFinder();
            $finder
                ->contains('@deprecated')
                ->exclude('vendor')
                ->exclude('Tests')
                ->exclude('Test')
                ->setParser($c['parser.deprecation'])
            ;

            return $finder;
        });

        // VIOLATION MESSAGEHELPER
        $c['violation.message_helper.class_message'] = function () {
            return new Renderer\MessageHelper\Message\ClassViolationMessage(
                'SensioLabs\DeprecationDetector\FileInfo\Usage\ClassUsage'
            );
        };

        $c['violation.message_helper.interface_message'] = function () {
            return new Renderer\MessageHelper\Message\InterfaceViolationMessage(
                'SensioLabs\DeprecationDetector\FileInfo\Usage\InterfaceUsage'
            );
        };

        $c['violation.message_helper.method_message'] = function () {
            return new Renderer\MessageHelper\Message\MethodViolationMessage(
                'SensioLabs\DeprecationDetector\FileInfo\Usage\MethodUsage'
            );
        };

        $c['violation.message_helper.method_definition_message'] = function () {
            return new Renderer\MessageHelper\Message\MethodDefinitionViolationMessage(
                'SensioLabs\DeprecationDetector\FileInfo\MethodDefinition'
            );
        };

        $c['violation.message_helper.supertype_message'] = function () {
            return new Renderer\MessageHelper\Message\SuperTypeViolationMessage(
                'SensioLabs\DeprecationDetector\FileInfo\Usage\SuperTypeUsage'
            );
        };

        $c['violation.message_helper'] = function ($c) {
            $messageHelper = new Renderer\MessageHelper\MessageHelper();

            $messageHelper->addViolationMessage($c['violation.message_helper.class_message']);
            $messageHelper->addViolationMessage($c['violation.message_helper.interface_message']);
            $messageHelper->addViolationMessage($c['violation.message_helper.method_message']);
            $messageHelper->addViolationMessage($c['violation.message_helper.method_definition_message']);
            $messageHelper->addViolationMessage($c['violation.message_helper.supertype_message']);

            return $messageHelper;
        };

        // VIOLATION RENDERER
        $c['violation.renderer'] = function ($c) {
            return new Renderer\ConsoleOutputRenderer(new ConsoleOutput(), $c['violation.message_helper']);
        };

        // ANCESTOR RESOLVER
        // TODO: fix container injection
        $c['ancestor_resolver'] = function ($c) {
            return new AncestorResolver($c);
        };

        $c['violation_checker'] = function ($c) {
            return new ComposedViolationChecker(
                [
                    new ClassViolationChecker(),
                    new InterfaceViolationChecker(),
                    new MethodViolationChecker($c['ancestor_resolver']),
                    new SuperTypeViolationChecker(),
                    new TypeHintViolationChecker(),
                    new MethodDefinitionViolationChecker($c['ancestor_resolver']),
                ]
            );
        };

        $c['violation_detector'] = function ($c) {
            return new ViolationDetector($c['event_dispatcher'], $c['violation_checker']);
        };
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
