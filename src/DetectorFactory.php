<?php

namespace SensioLabs\DeprecationDetector;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use SensioLabs\DeprecationDetector\Configuration\Configuration;
use SensioLabs\DeprecationDetector\Console\Output\DefaultProgressOutput;
use SensioLabs\DeprecationDetector\Console\Output\VerboseProgressOutput;
use SensioLabs\DeprecationDetector\FileInfo\Deprecation\FunctionDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\Deprecation\MethodDeprecation;
use SensioLabs\DeprecationDetector\Finder\DeprecationFinderFactory;
use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;
use SensioLabs\DeprecationDetector\Finder\UsageFinderFactory;
use SensioLabs\DeprecationDetector\Parser\DeprecationParser;
use SensioLabs\DeprecationDetector\Parser\UsageParser;
use SensioLabs\DeprecationDetector\RuleSet\Cache;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\ComposerFactory;
use SensioLabs\DeprecationDetector\RuleSet\Loader\Composer\ComposerLoader;
use SensioLabs\DeprecationDetector\RuleSet\Loader\DirectoryLoader;
use SensioLabs\DeprecationDetector\RuleSet\Loader\FileLoader;
use SensioLabs\DeprecationDetector\RuleSet\Loader\LoaderInterface;
use SensioLabs\DeprecationDetector\RuleSet\DirectoryTraverser;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
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
use SensioLabs\DeprecationDetector\Violation\Renderer\Html\RendererFactory;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\ClassViolationMessage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\FunctionViolationMessage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\InterfaceViolationMessage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\LanguageDeprecationMessage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\MethodDefinitionViolationMessage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\MethodViolationMessage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\SuperTypeViolationMessage;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper;
use SensioLabs\DeprecationDetector\Violation\Renderer\PathFormatter\DefaultFormatter;
use SensioLabs\DeprecationDetector\Violation\Renderer\PathFormatter\ShortPathFormatter;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\ClassViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\ComposedViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\FunctionViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\InterfaceViolationChecker;
use SensioLabs\DeprecationDetector\Violation\ViolationChecker\LanguageViolationChecker;
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
use SensioLabs\DeprecationDetector\Visitor\Usage\FindFunctionCalls;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindInterfaces;
use SensioLabs\DeprecationDetector\Visitor\Usage\FindLanguageDeprecations;
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
            $deprecationProgressOutput,
            new UsageFinderFactory()
        );

        $this->ancestorResolver = new AncestorResolver($deprecationUsageParser);

        $ruleSetProgressOutput = new VerboseProgressOutput(
            new ProgressBar($output),
            $configuration->isVerbose(),
            'RuleSet generation'
        );
        $ruleSetDeprecationParser = $this->getDeprecationParser();
        $ruleSetDeprecationFinder = new ParsedPhpFileFinder(
            $ruleSetDeprecationParser,
            $ruleSetProgressOutput,
            new DeprecationFinderFactory()
        );
        $deprecationDirectoryTraverser = new DirectoryTraverser($ruleSetDeprecationFinder);

        $violationDetector = $this->getViolationDetector($configuration);

        $renderer = $this->getRenderer($configuration, $output);

        $ruleSetLoader = $this->getRuleSetLoader($deprecationDirectoryTraverser, $configuration);

        $progressOutput = new DefaultProgressOutput($output, new Stopwatch());

        return new DeprecationDetector(
            $this->getPredefinedRuleSet(),
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
            new FindLanguageDeprecations(),
            new FindFunctionCalls(),
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
                new FunctionViolationChecker(),
                new LanguageViolationChecker(),
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

    /**
     * @param Configuration   $configuration
     * @param OutputInterface $output
     *
     * @return ConsoleOutputRenderer|Violation\Renderer\Html\Renderer
     */
    private function getRenderer(Configuration $configuration, OutputInterface $output)
    {
        $messageHelper = $this->getMessageHelper();

        if ($logFilePath = $configuration->logHtml()) {
            $factory = new RendererFactory($messageHelper, new Filesystem());

            return $factory->createHtmlOutputRenderer($logFilePath);
        }

        $formatter = ($prefix = $configuration->shortPath()) ?
            new ShortPathFormatter($prefix) :
            new DefaultFormatter();

        return new ConsoleOutputRenderer($output, $messageHelper, $formatter);
    }

    /**
     * @return MessageHelper
     */
    private function getMessageHelper()
    {
        return new MessageHelper(
            array(
                new ClassViolationMessage(),
                new InterfaceViolationMessage(),
                new MethodViolationMessage(),
                new MethodDefinitionViolationMessage(),
                new SuperTypeViolationMessage(),
                new LanguageDeprecationMessage(),
                new FunctionViolationMessage(),
            )
        );
    }

    /**
     * DeprecationParser.
     */

    /**
     * @return DeprecationParser
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
            $loader = new ComposerLoader($traverser, $ruleSetCache, new ComposerFactory());
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

    /**
     * @return RuleSet
     */
    private function getPredefinedRuleSet()
    {
        $deprecatedPhpMethods = array(
            'IntlDateFormatter' => array(
                'setTimeZoneID' => new MethodDeprecation('IntlDateFormatter', 'setTimeZoneID', 'Since PHP 5.5 use IntlDateFormatter->setTimeZone() instead'),
            ),
        );
        $deprecatedPhpFunctions = array(
            'call_user_method' => new FunctionDeprecation('call_user_method', 'Since PHP 4.1, use call_user_func() instead'),
            'call_user_method_array' => new FunctionDeprecation('call_user_method_array', 'Since PHP 4.1, call_user_func_array() instead'),
            'define_syslog_variables' => new FunctionDeprecation('define_syslog_variables', 'Since PHP 5.3'),
            'dl' => new FunctionDeprecation('dl', 'Since PHP 5.3'),
            'ereg' => new FunctionDeprecation('ereg', 'Since PHP 5.3, use  preg_match() instead'),
            'ereg_replace' => new FunctionDeprecation('ereg_replace', 'Since PHP 5.3, use  preg_replace() instead'),
            'eregi' => new FunctionDeprecation('eregi', 'Since PHP 5.3, use preg_match() with the "i" modifier instead'),
            'eregi_replace' => new FunctionDeprecation('eregi_replace', 'Since PHP 5.3, use preg_match() with the "i" modifier instead'),
            'set_magic_quotes_runtime' => new FunctionDeprecation('set_magic_quotes_runtime', 'Since PHP 5.3'),
            'magic_quotes_runtime' => new FunctionDeprecation('magic_quotes_runtime', 'Since PHP 5.3'),
            'session_register' => new FunctionDeprecation('session_register', 'Since PHP 5.3, use the $_SESSION superglobal instead'),
            'session_unregister' => new FunctionDeprecation('session_unregister', 'Since PHP 5.3, use the $_SESSION superglobal instead'),
            'session_is_registered' => new FunctionDeprecation('session_is_registered', 'Since PHP 5.3, use the $_SESSION superglobal instead'),
            'set_socket_blocking' => new FunctionDeprecation('set_socket_blocking', 'Since PHP 5.3, use stream_set_blocking() instead'),
            'split' => new FunctionDeprecation('split', 'Since PHP 5.3, use preg_split() instead'),
            'spliti' => new FunctionDeprecation('spliti', 'Since PHP 5.3, use preg_split() with the "i" modifier instead)'),
            'sql_regcase' => new FunctionDeprecation('sql_regcase', 'Since PHP 5.3'),
            'mysql_db_query' => new FunctionDeprecation('mysql_db_query', 'Since PHP 5.3, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_escape_string' => new FunctionDeprecation('mysql_escape_string', 'Since PHP 4.3, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_list_dbs' => new FunctionDeprecation('mysql_list_dbs', 'Since PHP 5.3, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'get_magic_quotes_gpc' => new FunctionDeprecation('get_magic_quotes_gpc', 'Since PHP 5.4'),
            'get_magic_quotes_runtime' => new FunctionDeprecation('get_magic_quotes_runtime', 'Since PHP 5.4'),
            'mcrypt_generic_end' => new FunctionDeprecation('mcrypt_generic_end', 'Since PHP 5.4'),
            'mcrypt_cbc' => new FunctionDeprecation('mcrypt_cbc', 'Since PHP 5.5'),
            'mcrypt_cfb' => new FunctionDeprecation('mcrypt_cfb', 'Since PHP 5.5'),
            'mcrypt_ecb' => new FunctionDeprecation('mcrypt_ecb', 'Since PHP 5.5'),
            'mcrypt_ofb' => new FunctionDeprecation('mcrypt_ofb', 'Since PHP 5.5'),
            'mysql_affected_rows' => new FunctionDeprecation('mysql_affected_rows', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_client_encoding' => new FunctionDeprecation('mysql_client_encoding', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_close' => new FunctionDeprecation('mysql_close', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_connect' => new FunctionDeprecation('mysql_connect', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_create_db' => new FunctionDeprecation('mysql_create_db', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_data_seek' => new FunctionDeprecation('mysql_data_seek', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_db_name' => new FunctionDeprecation('mysql_db_name', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_drop_db' => new FunctionDeprecation('mysql_drop_db', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_errno' => new FunctionDeprecation('mysql_errno', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_error' => new FunctionDeprecation('mysql_error', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_fetch_array' => new FunctionDeprecation('mysql_fetch_array', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_fetch_assoc' => new FunctionDeprecation('mysql_fetch_assoc', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_fetch_field' => new FunctionDeprecation('mysql_fetch_field', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_fetch_lengths' => new FunctionDeprecation('mysql_fetch_lengths', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_fetch_object' => new FunctionDeprecation('mysql_fetch_object', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_fetch_row' => new FunctionDeprecation('mysql_fetch_row', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_field_flags' => new FunctionDeprecation('mysql_field_flags', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_field_len' => new FunctionDeprecation('mysql_field_len', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_field_name' => new FunctionDeprecation('mysql_field_name', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_field_seek' => new FunctionDeprecation('mysql_field_seek', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_field_table' => new FunctionDeprecation('mysql_field_table', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_field_type' => new FunctionDeprecation('mysql_field_type', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_free_result' => new FunctionDeprecation('mysql_free_result', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_get_client_info' => new FunctionDeprecation('mysql_get_client_info', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_get_host_info' => new FunctionDeprecation('mysql_get_host_info', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_get_proto_info' => new FunctionDeprecation('mysql_get_proto_info', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_get_server_info' => new FunctionDeprecation('mysql_get_server_info', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_info' => new FunctionDeprecation('mysql_info', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_insert_id' => new FunctionDeprecation('mysql_insert_id', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_list_fields' => new FunctionDeprecation('mysql_list_fields', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_list_processes' => new FunctionDeprecation('mysql_list_processes', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_list_tables' => new FunctionDeprecation('mysql_list_tables', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_num_fields' => new FunctionDeprecation('mysql_num_fields', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_num_rows' => new FunctionDeprecation('mysql_num_rows', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_pconnect' => new FunctionDeprecation('mysql_pconnect', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_ping' => new FunctionDeprecation('mysql_ping', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_query' => new FunctionDeprecation('mysql_query', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_real_escape_string' => new FunctionDeprecation('mysql_real_escape_string', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_result' => new FunctionDeprecation('mysql_result', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_select_db' => new FunctionDeprecation('mysql_select_db', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_set_charset' => new FunctionDeprecation('mysql_set_charset', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_stat' => new FunctionDeprecation('mysql_stat', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_tablename' => new FunctionDeprecation('mysql_tablename', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_thread_id' => new FunctionDeprecation('mysql_thread_id', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_unbuffered_query' => new FunctionDeprecation('mysql_unbuffered_query', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql' => new FunctionDeprecation('mysql', 'Since 5.3.0 Use mysql_db_query instead.'),
            'mysql_fieldtable' => new FunctionDeprecation('mysql_fieldtable', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_fieldlen' => new FunctionDeprecation('mysql_fieldlen', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_fieldtype' => new FunctionDeprecation('mysql_fieldtype', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_fieldflags' => new FunctionDeprecation('mysql_fieldflags', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_selectdb' => new FunctionDeprecation('mysql_selectdb', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_freeresult' => new FunctionDeprecation('mysql_freeresult', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_numfields' => new FunctionDeprecation('mysql_numfields', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_numrows' => new FunctionDeprecation('mysql_numrows', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_listdbs' => new FunctionDeprecation('mysql_listdbs', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_listtables' => new FunctionDeprecation('mysql_listtables', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_listfields' => new FunctionDeprecation('mysql_listfields', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_dbname' => new FunctionDeprecation('mysql_dbname', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'mysql_table_name' => new FunctionDeprecation('mysql_table_name', 'Since PHP 5.5, The original MySQL extension is deprecated use the MySQLi or PDO_MySQL extensions instead.'),
            'datefmt_set_timezone_id' => new FunctionDeprecation('datefmt_set_timezone_id', 'Since PHP 5.5 use datefmt_set_timezone() instead'),
            'import_request_variables' => new FunctionDeprecation('import_request_variables', 'This function has been DEPRECATED as of PHP 5.3.0 and REMOVED as of PHP 5.4.0.'),
            'php_logo_guid' => new FunctionDeprecation('php_logo_guid', '5.5 Removed in PHP 5.5 Gets the logo guid'),
            'php_real_logo_guid' => new FunctionDeprecation('php_real_logo_guid', '5.5 Removed in PHP 5.5'),
            'php_egg_logo_guid' => new FunctionDeprecation('php_real_logo_guid', '5.5 Removed in PHP 5.5'),
            'zend_logo_guid' => new FunctionDeprecation('zend_logo_guid', '5.5 Removed in PHP 5.5'),
            'key_exists' => new FunctionDeprecation('key_exists', 'Since PHP 4.0.7'),
        );

        return new RuleSet(array(), array(), $deprecatedPhpMethods, $deprecatedPhpFunctions);
    }
}
