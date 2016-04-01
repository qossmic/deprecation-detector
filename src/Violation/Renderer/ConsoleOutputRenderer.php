<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer;

use PhpParser\Error;
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

        $table = new Table($this->output);
        $table->setHeaders(['#', 'Usage', 'Line', 'Comment']);

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

            $table->addRow([
                ++$i,
                $this->messageHelper->getViolationMessage($violation),
                $violation->getLine(),
                $violation->getComment(),
            ]);
        }

        $table->render();
    }

    /**
     * @param Error[] $errors
     */
    private function printErrors(array $errors)
    {
        if (0 === count($errors)) {
            return;
        }

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

    /**
     * @param PhpFileInfo $file
     *
     * @return TableCell[]
     */
    protected function getFileHeader(PhpFileInfo $file)
    {
        $cell = new TableCell(
            sprintf('<comment>%s</comment>', $file->getPathname()),
            ['colspan' => 3]
        );

        return [new TableCell(), $cell];
    }
}
