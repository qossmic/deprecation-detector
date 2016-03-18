<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper;

use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\ViolationMessageInterface;
use SensioLabs\DeprecationDetector\Violation\Violation;

class MessageHelper
{
    /**
     * @var ViolationMessageInterface[]
     */
    private $violationMessages = [];

    /**
     * @param ViolationMessageInterface[] $violationMessages
     */
    public function __construct(array $violationMessages = [])
    {
        $this->violationMessages = $violationMessages;
    }

    /**
     * @param ViolationMessageInterface $message
     */
    public function addViolationMessage(ViolationMessageInterface $message)
    {
        $this->violationMessages[] = $message;
    }

    public function getViolationMessage(Violation $violation)
    {
        foreach ($this->violationMessages as $message) {
            if ($message->supports($violation->getUsage())) {
                return $message->message($violation->getUsage());
            }
        }

        // fallback
        $classNamespace = explode('\\', get_class($violation->getUsage()));

        return sprintf(
            'Deprecated %s <info>%s</info>',
            array_pop($classNamespace),
            $violation->getUsage()->name()
        );
    }
}
