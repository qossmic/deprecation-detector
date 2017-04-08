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

    /**
     * @param Violation[] $violations
     * @param Error[]     $errors
     */
    public function renderViolations(array $violations, array $errors)
    {
        $this->printViolations($violations);
        $this->printErrors($errors);
    }

    /**
     * @param Violation[] $violations
     */
    abstract protected function printViolations(array $violations);

    /**
     * @param Error[] $errors
     */
    protected function printErrors(array $errors)
    {
        if (0 === count($errors)) {
            return;
        }

        $this->output->writeln('');
        $this->output->writeln('<error>Your project contains invalid code:</error>');
        foreach ($errors as $error) {
            $this->output->writeln(
                sprintf(
                    '<error>%s</error>',
                    $error->getRawMessage()
                )
            );
        }
    }
}
