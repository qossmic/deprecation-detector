<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer;

use SensioLabs\DeprecationDetector\Violation\Violation;
use PhpParser\Error;

interface RendererInterface
{
    /**
     * @param Violation[] $violations
     */
    public function renderViolations(array $violations);

    /**
     * @param Error[] $errors
     */
    public function renderParserErrors(array $errors);
}
