<?php

namespace SensioLabs\DeprecationDetector;

use SensioLabs\DeprecationDetector\Console\Output\DefaultProgressOutput;
use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;
use SensioLabs\DeprecationDetector\RuleSet\Loader\DirectoryLoader;
use SensioLabs\DeprecationDetector\RuleSet\Loader\LoaderInterface;
use SensioLabs\DeprecationDetector\RuleSet\RuleSet;
use SensioLabs\DeprecationDetector\TypeGuessing\AncestorResolver;
use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationDetector;
use SensioLabs\DeprecationDetector\Violation\Renderer\RendererInterface;

class DeprecationDetector
{
    /**
     * @var RuleSet
     */
    private $preDefinedRuleSet;

    /**
     * @var LoaderInterface
     */
    private $ruleSetLoader;

    /**
     * @var ParsedPhpFileFinder
     */
    private $deprecationFinder;

    /**
     * @var ViolationDetector
     */
    private $violationDetector;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var DefaultProgressOutput
     */
    private $output;
    /**
     * @var DirectoryLoader
     */
    private $sourceRuleSetLoader;

    /**
     * @param RuleSet $preDefinedRuleSet
     * @param DirectoryLoader $sourceRuleSetLoader
     * @param LoaderInterface $ruleSetLoader
     * @param AncestorResolver $ancestorResolver
     * @param ParsedPhpFileFinder $deprecationFinder
     * @param ViolationDetector $violationDetector
     * @param RendererInterface $renderer
     * @param DefaultProgressOutput $output
     */
    public function __construct(
        RuleSet $preDefinedRuleSet,
        DirectoryLoader $sourceRuleSetLoader,
        LoaderInterface $ruleSetLoader,
        AncestorResolver $ancestorResolver,
        ParsedPhpFileFinder $deprecationFinder,
        ViolationDetector $violationDetector,
        RendererInterface $renderer,
        DefaultProgressOutput $output
    ) {
        $this->preDefinedRuleSet = $preDefinedRuleSet;
        $this->ruleSetLoader = $ruleSetLoader;
        $this->ancestorResolver = $ancestorResolver;
        $this->deprecationFinder = $deprecationFinder;
        $this->violationDetector = $violationDetector;
        $this->renderer = $renderer;
        $this->output = $output;
        $this->sourceRuleSetLoader = $sourceRuleSetLoader;
    }

    /**
     * @param string[]  $sources
     * @param string    $ruleSetArg
     *
     * @return Violation[]
     *
     * @throws \Exception
     */
    public function checkForDeprecations(array $sources, $ruleSetArg)
    {
        $this->output->startProgress();

        $this->output->startRuleSetGeneration();
        $ruleSet = $this->ruleSetLoader->loadRuleSet($ruleSetArg);
        $ruleSet->merge($this->preDefinedRuleSet);
        foreach ($sources as $source) {
            $ruleSet->merge($this->sourceRuleSetLoader->loadRuleSet($source));
        }

        $this->output->endRuleSetGeneration();
        $this->output->startUsageDetection();

        // TODO: Move to AncestorResolver not hard coded
        $lib = (is_dir($ruleSetArg) ? $ruleSetArg : realpath('vendor'));
        $sourcePaths = array();
        $sourcePaths = array_merge($sourcePaths, $sources);
        $sourcePaths[] = $lib;
        $this->ancestorResolver->setSourcePaths($sourcePaths);

        $results = array();
        $violations = array();
        foreach ($sources as $source) {
            $result = $this->deprecationFinder->parsePhpFiles($source);
            $results[] = $result;
            foreach ($this->violationDetector->getViolations($ruleSet, $result->parsedFiles()) as $violation) {
                $violations[] = $violation;
            }
        }

        $errors = array();
        $fileCount = 0;
        foreach ($results as $result) {
            $errors = array_merge($errors, $result->parserErrors());
            $fileCount += $result->fileCount();
        }
        $this->output->endUsageDetection();

        $this->output->startOutputRendering();
        $this->renderer->renderViolations($violations, $errors);
        $this->output->endOutputRendering();

        $this->output->endProgress($fileCount, count($violations));

        return $violations;
    }
}
