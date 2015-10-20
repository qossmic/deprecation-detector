<?php

namespace SensioLabs\DeprecationDetector\DeprecationDetector\Factory;

use SensioLabs\DeprecationDetector\DeprecationDetector\Configuration\Configuration;
use SensioLabs\DeprecationDetector\DeprecationDetector\DeprecationDetector;

interface FactoryInterface
{
    /**
     * @param Configuration $configuration
     *
     * @return DeprecationDetector
     */
    public function buildDetector(Configuration $configuration);
}
