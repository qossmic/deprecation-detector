<?php

namespace SensioLabs\DeprecationDetector\Finder;

use SensioLabs\DeprecationDetector\EventListener\ProgressEvent;

interface ProgressEventDispatcherInterface
{
    public function start(ProgressEvent $event);

    public function advance(ProgressEvent $event);

    public function end(ProgressEvent $event);
}