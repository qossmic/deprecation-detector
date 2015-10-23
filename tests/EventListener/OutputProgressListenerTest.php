<?php

namespace Tests\EventListener;

use Prophecy\Argument;
use SensioLabs\DeprecationDetector\EventListener\OutputProgressListener;
use SensioLabs\DeprecationDetector\EventListener\ProgressEvent;

class OutputProgressListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $listener = new OutputProgressListener($output->reveal());

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\EventListener\OutputProgressListener', $listener);
    }

    public function testEventSubscriberInterface()
    {
        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $listener = new OutputProgressListener($output->reveal());

        $this->assertTrue(is_array($listener->getSubscribedEvents()));
    }

    public function testProgressBarInit()
    {
        $reflClass = new \ReflectionClass('SensioLabs\DeprecationDetector\EventListener\OutputProgressListener');
        $prop = $reflClass->getProperty('progressBar');
        $prop->setAccessible(true);

        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $listener = new OutputProgressListener($output->reveal());
        $this->assertInstanceOf('Symfony\Component\Console\Helper\ProgressBar', $prop->getValue($listener));
    }

    public function testOnRuleSetStartProgress()
    {
        $reflClass = new \ReflectionClass('SensioLabs\DeprecationDetector\EventListener\OutputProgressListener');
        $prop = $reflClass->getProperty('progressBar');
        $prop->setAccessible(true);

        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $listener = new OutputProgressListener($output->reveal());

        $progressBar = $this->prophesize('Symfony\Component\Console\Helper\ProgressBar');
        $progressBar->setFormat(Argument::containingString('rule sets'))->shouldBeCalled();
        $progressBar->start(123)->shouldBeCalled();
        $prop->setValue($listener, $progressBar->reveal());

        $event = new ProgressEvent(0, 123);
        $listener->onRuleSetAdvance($event);
    }

    public function testOnCheckerStartProgress()
    {
        $reflClass = new \ReflectionClass('SensioLabs\DeprecationDetector\EventListener\OutputProgressListener');
        $prop = $reflClass->getProperty('progressBar');
        $prop->setAccessible(true);

        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $listener = new OutputProgressListener($output->reveal());

        $progressBar = $this->prophesize('Symfony\Component\Console\Helper\ProgressBar');
        $progressBar->setFormat(Argument::containingString('deprecations'))->shouldBeCalled();
        $progressBar->start(123)->shouldBeCalled();
        $prop->setValue($listener, $progressBar->reveal());

        $event = new ProgressEvent(0, 123);
        $listener->onCheckerAdvance($event);
    }

    public function testOnRuleSetAdvanceProgress()
    {
        $reflClass = new \ReflectionClass('SensioLabs\DeprecationDetector\EventListener\OutputProgressListener');
        $prop = $reflClass->getProperty('progressBar');
        $prop->setAccessible(true);

        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $listener = new OutputProgressListener($output->reveal());

        $progressBar = $this->prophesize('Symfony\Component\Console\Helper\ProgressBar');
        $progressBar->advance()->shouldBeCalled();
        $prop->setValue($listener, $progressBar->reveal());

        $event = new ProgressEvent(45, 123);
        $listener->onRuleSetAdvance($event);
    }

    public function testOnRuleSetEndProgress()
    {
        $reflClass = new \ReflectionClass('SensioLabs\DeprecationDetector\EventListener\OutputProgressListener');
        $prop = $reflClass->getProperty('progressBar');
        $prop->setAccessible(true);

        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $listener = new OutputProgressListener($output->reveal());

        $progressBar = $this->prophesize('Symfony\Component\Console\Helper\ProgressBar');
        $progressBar->finish()->shouldBeCalled();
        $prop->setValue($listener, $progressBar->reveal());

        $event = new ProgressEvent(123, 123);
        $listener->onRuleSetAdvance($event);
    }
}
