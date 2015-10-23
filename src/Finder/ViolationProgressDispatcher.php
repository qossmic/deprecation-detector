<?php

namespace SensioLabs\DeprecationDetector\Finder;

use SensioLabs\DeprecationDetector\EventListener\ProgressEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ViolationProgressDispatcher implements ProgressEventDispatcherInterface
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function start(ProgressEvent $event)
    {
        $this->dispatcher->dispatch(
            ProgressEvent::START_CHECKER,
            $event
        );
    }

    public function advance(ProgressEvent $event)
    {
        $this->dispatcher->dispatch(
            ProgressEvent::ADVANCE_CHECKER,
            $event
        );
    }

    public function end(ProgressEvent $event)
    {
        $this->dispatcher->dispatch(
            ProgressEvent::DONE_CHECKER,
            $event
        );
    }
}