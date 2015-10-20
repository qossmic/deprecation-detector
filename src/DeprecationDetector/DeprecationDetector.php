<?php

namespace SensioLabs\DeprecationDetector\DeprecationDetector;

use SensioLabs\DeprecationDetector\Violation\ViolationDetector;
use SensioLabs\DeprecationDetector\Parser\ParserInterface;
use SensioLabs\DeprecationDetector\Violation\Renderer\RendererInterface;

class DeprecationDetector
{
    /**
     * @var ParserInterface
     */
    private $ruleSetParser;

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
     * @param ParserInterface   $ruleSetParser
     * @param ParserInterface   $deprecationUsageParser
     * @param ViolationDetector $violationDetector
     * @param RendererInterface $renderer
     */
    public function __construct(
        ParserInterface $ruleSetParser,
        ParserInterface $deprecationUsageParser,
        ViolationDetector $violationDetector,
        RendererInterface $renderer
    ) {
        $this->ruleSetParser = $ruleSetParser;
        $this->deprecationUsageParser = $deprecationUsageParser;
        $this->violationDetector = $violationDetector;
        $this->renderer = $renderer;
    }

    /**
     * @param string $source
     * @param string $destination
     */
    public function checkForDeprecations($source, $destination)
    {
    }
}
