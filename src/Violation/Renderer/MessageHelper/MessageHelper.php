<?php

namespace SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper;

use SensioLabs\DeprecationDetector\Violation\Renderer\MessageHelper\Message\ViolationMessageInterface;
use SensioLabs\DeprecationDetector\Violation\Violation;

class MessageHelper
{
    /**
     * @var ViolationMessageInterface[]
     */
    private $messages = array();

    /**
     * @param ViolationMessageInterface $message
     */
    public function addViolationMessage(ViolationMessageInterface $message)
    {
        $this->messages[] = $message;
    }

    public function getViolationMessage(Violation $violation)
    {
        foreach ($this->messages as $message) {
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
