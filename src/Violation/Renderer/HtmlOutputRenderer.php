<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer;

use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper;
use SensioLabs\DeprecationDetector\Violation\Violation;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class HtmlOutputRenderer implements RendererInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var MessageHelper
     */
    private $messageHelper;
    /**
     * @var string
     */
    private $outputFilename;
    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @param OutputInterface $output
     * @param MessageHelper $messageHelper
     * @param Filesystem $filesystem
     * @param $outputFilename
     */
    public function __construct(
        OutputInterface $output,
        MessageHelper $messageHelper,
        Filesystem $filesystem,
        $outputFilename
    ) {
        $this->output = $output;
        $this->messageHelper = $messageHelper;
        $this->fileSystem = $filesystem;
        $this->outputFilename = $outputFilename;
    }

    /**
     * @param Violation[] $violations
     */
    public function renderViolations(array $violations)
    {
        $orderedViolations = array();
        // sorting and grouping violations
        foreach ($violations as  $violation) {

            $key = $violation->getFile()->getPathname();
            if (!array_key_exists($key, $orderedViolations)) {
                $orderedViolations[$key] = array();
            }

            $fileViolation['message'] = $this->messageHelper->getViolationMessage($violation);
            $fileViolation['line'] = $violation->getLine();
            $fileViolation['comment'] = $violation->getComment();
            $orderedViolations[$key][] = $fileViolation;
        }

        ob_start();
        include __DIR__.'/../../Resources/templates/htmlTableOutput.phtml';
        $htmlOutput = ob_get_clean();

        $this->output->writeln(sprintf('Rendered HTML to %s', $this->getOutputFilename()));

        $this->fileSystem->mkdir(dirname($this->getOutputFilename()));

        file_put_contents($this->getOutputFilename(), $htmlOutput);
    }

    /**
     * @return string
     */
    public function getOutputFilename()
    {
        return $this->outputFilename;
    }

    /**
     * @param string $outputFilename
     */
    public function setOutputFilename($outputFilename)
    {
        $this->outputFilename = $outputFilename;
    }
}
