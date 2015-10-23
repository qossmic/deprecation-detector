<?php

namespace SensioLabs\DeprecationDetector\DeprecationDetector;

use SensioLabs\DeprecationDetector\RuleSet\Loader\LoaderInterface;
use SensioLabs\DeprecationDetector\Violation\ViolationDetector;
use SensioLabs\DeprecationDetector\Parser\ParserInterface;
use SensioLabs\DeprecationDetector\Violation\Renderer\RendererInterface;

class DeprecationDetector
{
    /**
     * @var LoaderInterface
     */
    private $ruleSetLoader;

    /**
     * @var ParserInterface
     */
    private $deprecationUsageParser;

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
     * @param ParserInterface   $deprecationUsageParser
     * @param ViolationDetector $violationDetector
     * @param RendererInterface $renderer
     */
    public function __construct(
        LoaderInterface $ruleSetLoader,
        ParserInterface $deprecationUsageParser,
        ViolationDetector $violationDetector,
        RendererInterface $renderer
    ) {
        $this->ruleSetLoader = $ruleSetLoader;
        $this->deprecationUsageParser = $deprecationUsageParser;
        $this->violationDetector = $violationDetector;
        $this->renderer = $renderer;
    }

    /**
     * @param string $source
     * @param string $ruleSet
     *
     * @throws \Exception
     */
    public function checkForDeprecations($source, $ruleSet)
    {
        $ruleSet = $this->ruleSetLoader->loadRuleSet($ruleSet);
    }
}
