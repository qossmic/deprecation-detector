<?php

namespace SensioLabs\DeprecationDetector\DeprecationDetector;

use SensioLabs\DeprecationDetector\AncestorResolver;
use SensioLabs\DeprecationDetector\DeprecationDetector\Output\DefaultProgressOutput;
use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;
use SensioLabs\DeprecationDetector\RuleSet\Loader\LoaderInterface;
use SensioLabs\DeprecationDetector\Violation\Violation;
use SensioLabs\DeprecationDetector\Violation\ViolationDetector;
use SensioLabs\DeprecationDetector\Violation\Renderer\RendererInterface;

class DeprecationDetector
{
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
     * @param LoaderInterface          $ruleSetLoader
     * @param AncestorResolver         $ancestorResolver
     * @param ParsedPhpFileFinder      $deprecationFinder
     * @param ViolationDetector        $violationDetector
     * @param RendererInterface        $renderer
     * @param DefaultProgressOutput    $output
     */
    public function __construct(
        LoaderInterface $ruleSetLoader,
        AncestorResolver $ancestorResolver,
        ParsedPhpFileFinder $deprecationFinder,
        ViolationDetector $violationDetector,
        RendererInterface $renderer,
        DefaultProgressOutput $output
    ) {
        $this->ruleSetLoader = $ruleSetLoader;
        $this->ancestorResolver = $ancestorResolver;
        $this->deprecationFinder = $deprecationFinder;
        $this->violationDetector = $violationDetector;
        $this->renderer = $renderer;
        $this->output = $output;
    }

    /**
     * @param string $sourceArg
     * @param string $ruleSetArg
     *
     * @return Violation[]
     *
     * @throws \Exception
     */
    public function checkForDeprecations($sourceArg, $ruleSetArg)
    {
        $this->output->startProgress();

        $this->output->startRuleSetGeneration();
        $ruleSet = $this->ruleSetLoader->loadRuleSet($ruleSetArg);
        $this->output->endRuleSetGeneration();

        $this->output->startUsageDetection();
        // TODO: not hard coded
        $lib = (is_dir($ruleSetArg) ? $ruleSetArg : realpath('vendor'));
        $this->ancestorResolver->setSourcePaths(array(
            $sourceArg,
            $lib,
        ));

        /** @var ParsedPhpFileFinder $files */
        $files = $this->deprecationFinder->in($sourceArg);
        $violations = $this->violationDetector->getViolations($ruleSet, $files);
        $this->output->endUsageDetection();

        $this->output->startRendering();
        $this->renderer->renderViolations($violations);
        if ($files->hasParserErrors()) {
            $this->renderer->renderParserErrors($files->getParserErrors());
        }
        $this->output->endRendering();

        $this->output->endProgress($files->count(), count($violations));
        return $violations;
    }
}
