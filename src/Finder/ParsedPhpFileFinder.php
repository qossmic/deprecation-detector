<?php

namespace SensioLabs\DeprecationDetector\Finder;

use SensioLabs\DeprecationDetector\Console\Output\VerboseProgressOutput;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\Parser\ParserInterface;
use PhpParser\Error;

class ParsedPhpFileFinder
{
    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var VerboseProgressOutput
     */
    private $progressOutput;

    /**
     * @var FinderFactoryInterface
     */
    private $finderFactory;

    /**
     * @param ParserInterface        $parser
     * @param VerboseProgressOutput  $progressOutput
     * @param FinderFactoryInterface $finderFactory
     */
    public function __construct(ParserInterface $parser, VerboseProgressOutput $progressOutput, FinderFactoryInterface $finderFactory)
    {
        $this->parser = $parser;
        $this->progressOutput = $progressOutput;
        $this->finderFactory = $finderFactory;
    }

    /**
     * @param string $path
     *
     * @return Result
     */
    public function parsePhpFiles($path)
    {
        $files = $this->finderFactory->createFinder()->in($path);
        $parsedFiles = [];
        $parserErrors = [];

        $this->progressOutput->start($fileCount = $files->count());

        $i = 0;
        foreach ($files->getIterator() as $file) {
            $file = PhpFileInfo::create($file);

            try {
                $this->progressOutput->advance(++$i, $file);
                $this->parser->parseFile($file);
            } catch (Error $ex) {
                $raw = $ex->getRawMessage().' in file '.$file;
                $ex->setRawMessage($raw);
                $parserErrors[] = $ex;
            }

            $parsedFiles[] = $file;
        }

        $this->progressOutput->end();

        return new Result($parsedFiles, $parserErrors, $fileCount);
    }
}
