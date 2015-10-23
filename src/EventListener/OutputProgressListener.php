<?php

namespace SensioLabs\DeprecationDetector\EventListener;

use SensioLabs\DeprecationDetector\EventListener\ProgressEvent;
use SensioLabs\DeprecationDetector\FileInfo\PhpFileInfo;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OutputProgressListener implements EventSubscriberInterface
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
            ProgressEvent::START_RULESET => 'onRuleSetStart',
            ProgressEvent::ADVANCE_RULESET => 'onRuleSetAdvance',
            ProgressEvent::GENERATED_RULESET => 'onRuleSetGenerated',
            ProgressEvent::START_CHECKER => 'onCheckerStart',
            ProgressEvent::ADVANCE_CHECKER => 'onCheckerAdvance',
            ProgressEvent::DONE_CHECKER => 'onCheckerDone'
        );
    }

    /**
     * @param ProgressEvent $event
     * @param string $label
     */
    private function onStart(ProgressEvent $event, $label)
    {
        $format = $label . ': <comment>loading %max% files into memory, this can take some time</comment>';
        $this->progressBar->setFormat($format);
        $this->progressBar->start($event->getTotalNumber());
    }

    /**
     * @param ProgressEvent $event
     * @param string $label
     */
    private function onAdvance(ProgressEvent $event, $label)
    {
        if (1 === $event->getProcessed()) {
            $format = '<info>%message%</info>' . "\n" . $label . ': <info>%current%</info>/<info>%max%</info>';
            $this->progressBar->clear();
            $this->progressBar->setFormat($format);
        }

        if ($event->getFile() instanceof PhpFileInfo) {
            $message = $event->getFile()->getRelativePathname();
            $this->progressBar->setMessage($message);
        }

        $this->progressBar->clear();
        $this->progressBar->advance();
        $this->progressBar->display();
    }

    private function onEnd()
    {
        $this->progressBar->clear();
        $this->progressBar->finish();
    }

    /**
     * @param ProgressEvent $event
     */
    public function onRuleSetStart(ProgressEvent $event)
    {
        $this->onStart($event, 'RuleSet Generation');
    }

    /**
     * @param ProgressEvent $event
     */
    public function onRuleSetAdvance(ProgressEvent $event)
    {
        $this->onAdvance($event, 'RuleSet Generation');
    }

    /**
     * @param ProgressEvent $event
     */
    public function onRuleSetGenerated(ProgressEvent $event)
    {
        $this->onEnd($event, 'RuleSet Generation');
    }

    /**
     * @param ProgressEvent $event
     */
    public function onCheckerStart(ProgressEvent $event)
    {
        $this->onStart($event, 'Deprecation detection');
    }

    /**
     * @param ProgressEvent $event
     */
    public function onCheckerAdvance(ProgressEvent $event)
    {
        $this->onAdvance($event, 'Deprecation detection');
    }

    /**
     * @param ProgressEvent $event
     */
    public function onCheckerDone(ProgressEvent $event)
    {
        $this->onEnd($event, 'Deprecation detection');
    }
}
