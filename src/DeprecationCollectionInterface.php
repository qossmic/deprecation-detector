<?php

namespace SensioLabs\DeprecationDetector;

use SensioLabs\DeprecationDetector\FileInfo\Deprecation\ClassDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\Deprecation\InterfaceDeprecation;
use SensioLabs\DeprecationDetector\FileInfo\Deprecation\MethodDeprecation;

interface DeprecationCollectionInterface
{
    /**
     * @return ClassDeprecation[]
     */
    public function classDeprecations();

    /**
     * @return InterfaceDeprecation[]
     */
    public function interfaceDeprecations();

    /**
     * @return MethodDeprecation[]
     */
    public function methodDeprecations();
}
