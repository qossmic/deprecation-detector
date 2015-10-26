<?php

namespace SensioLabs\DeprecationDetector\Console;

use SensioLabs\DeprecationDetector\Console\Command\CheckCommand;
use SensioLabs\DeprecationDetector\EventListener\CommandListener;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Application extends BaseApplication
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    public function __construct()
    {
        parent::__construct('SensioLabs Deprecation Detector', 'dev');
        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber(new CommandListener());

        $checkCommand = new CheckCommand('check');
        $this->setDispatcher($this->dispatcher);
        $this->add($checkCommand);
        $this->setDefaultCommand('check');
    }

    /**
     * @return EventDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }
}
