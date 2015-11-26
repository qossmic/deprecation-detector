<?php

namespace SensioLabs\DeprecationDetector\Console;

use SensioLabs\DeprecationDetector\Console\Command\CheckCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('SensioLabs Deprecation Detector', 'dev');
        $checkCommand = new CheckCommand('check');
        $this->add($checkCommand);
        $this->setDefaultCommand('check');
    }
}