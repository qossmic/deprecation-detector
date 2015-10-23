<?php

namespace SensioLabs\DeprecationDetector\DeprecationDetector;

use SensioLabs\DeprecationDetector\AncestorResolver;
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
     * @param LoaderInterface   $ruleSetLoader
     * @param AncestorResolver  $ancestorResolver
     * @param ParsedPhpFileFinder   $deprecationFinder
     * @param ViolationDetector $violationDetector
     * @param RendererInterface $renderer
     */
    public function __construct(
        LoaderInterface $ruleSetLoader,
        AncestorResolver $ancestorResolver,
        ParsedPhpFileFinder $deprecationFinder,
        ViolationDetector $violationDetector,
        RendererInterface $renderer
    ) {
        $this->ruleSetLoader = $ruleSetLoader;
        $this->ancestorResolver = $ancestorResolver;
        $this->deprecationFinder = $deprecationFinder;
        $this->violationDetector = $violationDetector;
        $this->renderer = $renderer;
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
        $ruleSet = $this->ruleSetLoader->loadRuleSet($ruleSetArg);

        // TODO: not hard coded
        $lib = (is_dir($ruleSetArg) ? $ruleSetArg : realpath('vendor'));
        $this->ancestorResolver->setSourcePaths(array(
            $sourceArg,
            $lib
        ));

        /** @var ParsedPhpFileFinder $files */
        $files = $this->deprecationFinder->in($sourceArg);
        $violations = $this->violationDetector->getViolations($ruleSet, $files);

        $this->renderer->renderViolations($violations);
        if ($files->hasParserErrors()) {
            $this->renderer->renderParsingErrors($files->getParserErrors());
        }

        return $violations;
    }
}
