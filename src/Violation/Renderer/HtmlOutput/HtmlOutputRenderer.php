<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\HtmlOutput;

use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper;
use SensioLabs\DeprecationDetector\Violation\Renderer\RendererInterface;
use SensioLabs\DeprecationDetector\Violation\Violation;
use Symfony\Component\Filesystem\Filesystem;

class HtmlOutputRenderer implements RendererInterface
{
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
     * @param MessageHelper $messageHelper
     * @param Filesystem $filesystem
     * @param $outputFilename
     */
    public function __construct(
        MessageHelper $messageHelper,
        Filesystem $filesystem,
        $outputFilename
    ) {
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
        include __DIR__.'/../../../Resources/templates/htmlTableOutput.phtml';
        $htmlOutput = ob_get_clean();

        $this->fileSystem->mkdir(dirname($this->outputFilename));

        file_put_contents($this->outputFilename, $htmlOutput);
    }
}
