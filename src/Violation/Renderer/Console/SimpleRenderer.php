<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\Console;

use PhpParser\Error;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper;
use SensioLabs\DeprecationDetector\Violation\Violation;
use Symfony\Component\Console\Output\OutputInterface;

class SimpleRenderer extends BaseRenderer
{
    /**
     * @param OutputInterface $output
     * @param MessageHelper   $messageHelper
     */
    public function __construct(OutputInterface $output, MessageHelper $messageHelper)
    {
        parent::__construct($output, $messageHelper);
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
    private function printViolations(array $violations)
    {
        if (0 === count($violations)) {
            return;
        }

        $tmpFile = null;
        foreach ($violations as $i => $violation) {
            if ($tmpFile !== $violation->getFile()) {
                $tmpFile = $violation->getFile();
                if (0 !== $i) {
                    $this->output->writeln("");
                }
                $this->output->writeln($tmpFile->getRelativePathname());
            }

            $this->output->writeln(sprintf(
                "Nr. %d line %d: %s",
                ++$i,
                $violation->getLine(),
                $this->messageHelper->getViolationMessage($violation)
            ));
        }
    }

    /**
     * @param Error[] $errors
     */
    private function printErrors(array $errors)
    {
        if (0 === count($errors)) {
            return;
        }

    }
}
