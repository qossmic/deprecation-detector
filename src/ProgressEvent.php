<?php

namespace SensioLabs\DeprecationDetector;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ProgressEvent.
 *
 * @author Christopher Hertel <christopher.hertel@sensiolabs.de>
 */
class ProgressEvent extends Event
{
    const RULESET = 'progress.ruleset';
    const USAGE = 'progress.usage';
    const CHECKER = 'progress.checker';

    /**
     * @var int
     */
    protected $processed;

    /**
     * @var int
     */
    protected $totalNumber;

    /**
     * @param int $processed
     * @param int $totalNumber
     */
    public function __construct($processed, $totalNumber)
    {
        $this->processed = $processed;
        $this->totalNumber = $totalNumber;
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
}
