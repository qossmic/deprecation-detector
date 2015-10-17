<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer;

use SensioLabs\DeprecationDetector\Violation\Violation;

interface RendererInterface
{
    /**
     * @param Violation[] $violations
     */
    public function renderViolations(array $violations);
}
