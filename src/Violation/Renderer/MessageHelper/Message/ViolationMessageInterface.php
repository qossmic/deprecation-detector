<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message;

use SensioLabs\DeprecationDetector\FileInfo\Usage\UsageInterface;

interface ViolationMessageInterface
{
    /**
     * @param UsageInterface $usage
     *
     * @return bool
     */
    public function supports(UsageInterface $usage);

    /**
     * @param UsageInterface $usage
     *
     * @return string
     */
    public function message(UsageInterface $usage);
}
