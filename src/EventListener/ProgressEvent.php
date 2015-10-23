<?php

namespace SensioLabs\DeprecationDetector\EventListener;

use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use SensioLabs\DeprecationDetector\Finder\ParsedPhpFileFinder;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ProgressEvent.
 *
 * @author Christopher Hertel <christopher.hertel@sensiolabs.de>
 */
class ProgressEvent extends Event
{
    const START_RULESET = 'progress.ruleset.start';

    const ADVANCE_RULESET = 'progress.ruleset.advance';

    const GENERATED_RULESET = 'progress.ruleset.generated';

    /** @TODO UNKNOWN UNTIL NOW */
    const USAGE = 'progress.usage';

    const START_CHECKER = 'progress.checker.start';

    const ADVANCE_CHECKER = 'progress.checker.advance';

    const DONE_CHECKER = 'progress.checker.done';

    /**
     * @var int
     */
    protected $processed;

    /**
     * @var int
     */
    protected $totalNumber;

    protected $file;

    /**
     * @param int $processed
     * @param int $totalNumber
     * @param PhpFileInfo $file
     */
    public function __construct($processed, $totalNumber, $file = null)
    {
        $this->processed = $processed;
        $this->totalNumber = $totalNumber;
        $this->file = $file;
    }

    /**
     * @return int
     */
    public function getProcessed()
    {
        return $this->processed;
    }

    /**
     * @return int
     */
    public function getTotalNumber()
    {
        return $this->totalNumber;
    }

    /**
     * @return PhpFileInfo|null
     */
    public function getFile()
    {
        return $this->file;
    }
}
