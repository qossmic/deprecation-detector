<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\Console;

use PhpParser\Error;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper;
use SensioLabs\DeprecationDetector\Violation\Renderer\RendererInterface;
use SensioLabs\DeprecationDetector\Violation\Violation;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseRenderer implements RendererInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var MessageHelper
     */
    protected $messageHelper;

    /**
     * @param OutputInterface $output
     * @param MessageHelper   $messageHelper
     */
    public function __construct(OutputInterface $output, MessageHelper $messageHelper)
    {
        $this->output = $output;
        $this->messageHelper = $messageHelper;
    }
}