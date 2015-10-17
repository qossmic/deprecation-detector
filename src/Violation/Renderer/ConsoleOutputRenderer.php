<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\MessageHelper;
use SensioLabs\DeprecationDetector\Violation\Violation;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputRenderer implements RendererInterface
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
     */
    public function renderViolations(array $violations)
    {
        $table = new Table($this->output);
        $table->setHeaders(array('#', 'Usage', 'Line', 'Comment'));

        $tmpFile = null;
        foreach ($violations as $i => $violation) {
            if ($tmpFile !== $violation->getFile()) {
                $tmpFile = $violation->getFile();
                if (0 !== $i) {
                    $table->addRow(new TableSeparator());
                }
                $table->addRow($this->getFileHeader($tmpFile));
                $table->addRow(new TableSeparator());
            }

            $table->addRow(array(
                ++$i,
                $this->messageHelper->getViolationMessage($violation),
                $violation->getLine(),
                $violation->getComment(),
            ));
        }

        $table->render();
    }

    /**
     * @param PhpFileInfo $file
     *
     * @return TableCell[]
     */
    protected function getFileHeader(PhpFileInfo $file)
    {
        $cell = new TableCell(
            sprintf('<comment>%s</comment>', $file->getPathname()),
            array('colspan' => 3)
        );

        return array(new TableCell(), $cell);
    }
}
