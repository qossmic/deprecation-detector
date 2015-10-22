<?php

namespace SensioLabs\DeprecationDetector\DeprecationDetector\Factory;

use SensioLabs\DeprecationDetector\DeprecationDetector\Configuration\Configuration;
use SensioLabs\DeprecationDetector\DeprecationDetector\DeprecationDetector;
use Symfony\Component\Console\Output\OutputInterface;

interface FactoryInterface
{
    /**
     * @param Configuration $configuration
     * @param OutputInterface $output
     *
     * @return DeprecationDetector
     */
    public function buildDetector(Configuration $configuration, OutputInterface $output);
}
