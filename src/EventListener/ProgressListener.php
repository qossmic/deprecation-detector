<?php

namespace SensioLabs\DeprecationDetector\EventListener;

use SensioLabs\DeprecationDetector\ProgressEvent;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProgressListener implements EventSubscriberInterface
{
    /**
     * @var ProgressBar
     */
    protected $progressBar;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->progressBar = new ProgressBar($output);
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ProgressEvent::RULESET => 'onRuleSetProgress',
            ProgressEvent::CHECKER => 'onCheckerProgress',
        );
    }

    /**
     * @param ProgressEvent $event
     */
    public function onRuleSetProgress(ProgressEvent $event)
    {
        $this->onProgress($event, 'Loading rule sets');
    }

    /**
     * @param ProgressEvent $event
     */
    public function onCheckerProgress(ProgressEvent $event)
    {
        $this->onProgress($event, 'Checking files for deprecations');
    }

    /**
     * @param ProgressEvent $event
     * @param string        $label
     */
    protected function onProgress($event, $label)
    {
        if (0 === $event->getProcessed()) {
            $format = $label.': <comment>loading %max% files</comment>';
            $this->progressBar->setFormat($format);
            $this->progressBar->start($event->getTotalNumber());
        } elseif ($event->getProcessed() == $event->getTotalNumber()) {
            $this->progressBar->finish();
            $this->output->writeln('');
        } else {
            if (1 === $event->getProcessed()) {
                $format = $label.': <info>%current%/%max%</info>';
                $this->progressBar->clear();
                $this->progressBar->setFormat($format);
            }
            $this->progressBar->advance();
        }
    }
}
