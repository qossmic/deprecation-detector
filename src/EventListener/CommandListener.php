<?php

namespace SensioLabs\DeprecationDetector\EventListener;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class CommandListener implements EventSubscriberInterface
{
    /**
     * Stopwatch instance.
     *
     * @var Stopwatch
     */
    protected $stopwatch;

    public function __construct()
    {
        $this->stopwatch = new Stopwatch();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ConsoleEvents::COMMAND => 'onConsoleCommand',
            ConsoleEvents::TERMINATE => 'onConsoleTerminate',
        );
    }

    /**
     * @param ConsoleCommandEvent $event
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        if ('check' == $event->getCommand()->getName()) {
            $this->stopwatch->start('check_command');
        }
    }

    /**
     * @param ConsoleTerminateEvent $event
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        if ('check' == $event->getCommand()->getName()) {
            $stopEvent = $this->stopwatch->stop('check_command');
            $output = $event->getOutput();

            $output->writeln(
                sprintf(
                    'Checked source files in %s seconds, %s MB memory used',
                    $stopEvent->getDuration() / 1000,
                    $stopEvent->getMemory() / 1024 / 1024
                )
            );
        }
    }
}
