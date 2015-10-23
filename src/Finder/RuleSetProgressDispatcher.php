<?php

namespace SensioLabs\DeprecationDetector\Finder;

use SensioLabs\DeprecationDetector\EventListener\ProgressEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RuleSetProgressDispatcher implements ProgressEventDispatcherInterface
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function start(ProgressEvent $event)
    {
        $this->dispatcher->dispatch(
            ProgressEvent::START_RULESET,
            $event
        );
    }

    public function advance(ProgressEvent $event)
    {
        $this->dispatcher->dispatch(
            ProgressEvent::ADVANCE_RULESET,
            $event
        );
    }

    public function end(ProgressEvent $event)
    {
        $this->dispatcher->dispatch(
            ProgressEvent::GENERATED_RULESET,
            $event
        );
    }
}