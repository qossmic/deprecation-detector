<?php

namespace SensioLabs\DeprecationDetector\Finder;

use Symfony\Component\Finder\Finder;

interface FinderFactoryInterface
{
    /**
     * @return Finder
     */
    public function createFinder();
}
