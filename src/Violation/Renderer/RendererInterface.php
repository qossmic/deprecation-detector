<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer;

use SensioLabs\DeprecationDetector\Violation\Violation;
use PhpParser\Error;

interface RendererInterface
{
    /**
     * @param Violation[] $violations
     * @param Error[] $errors
     */
    public function renderViolations(array $violations, array $errors);
}
