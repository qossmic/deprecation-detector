<?php

namespace Tests\EventListener;

use Prophecy\Argument;
use SensioLabs\DeprecationDetector\EventListener\CommandListener;

class CommandListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassIsInitializable()
    {
        $listener = new CommandListener();

        $this->assertInstanceOf('SensioLabs\DeprecationDetector\EventListener\CommandListener', $listener);
    }

    public function testEventSubscriberInterface()
    {
        $listener = new CommandListener();

        $this->assertTrue(is_array($listener->getSubscribedEvents()));
    }

    public function testStopwatchInit()
    {
        $reflClass = new \ReflectionClass('SensioLabs\DeprecationDetector\EventListener\CommandListener');
        $prop = $reflClass->getProperty('stopwatch');
        $prop->setAccessible(true);

        $listener = new CommandListener();
        $this->assertInstanceOf('Symfony\Component\Stopwatch\Stopwatch', $prop->getValue($listener));
    }

    public function testOnCommandCheck()
    {
        $reflClass = new \ReflectionClass('SensioLabs\DeprecationDetector\EventListener\CommandListener');
        $prop = $reflClass->getProperty('stopwatch');
        $prop->setAccessible(true);

        $command = $this->prophesize('Symfony\Component\Console\Command\Command');
        $command->getName()->shouldBeCalled()->willReturn('check');
        $event = $this->prophesize('Symfony\Component\Console\Event\ConsoleCommandEvent');
        $event->getCommand()->shouldBeCalled()->willReturn($command->reveal());

        $listener = new CommandListener();
        $listener->onConsoleCommand($event->reveal());
        $stopwatch = $prop->getValue($listener);

        $this->assertTrue($stopwatch->isStarted('check_command'));
    }

    public function testOnCommandOther()
    {
        $reflClass = new \ReflectionClass('SensioLabs\DeprecationDetector\EventListener\CommandListener');
        $prop = $reflClass->getProperty('stopwatch');
        $prop->setAccessible(true);

        $command = $this->prophesize('Symfony\Component\Console\Command\Command');
        $command->getName()->shouldBeCalled()->willReturn('other');
        $event = $this->prophesize('Symfony\Component\Console\Event\ConsoleCommandEvent');
        $event->getCommand()->shouldBeCalled()->willReturn($command->reveal());

        $listener = new CommandListener();
        $listener->onConsoleCommand($event->reveal());
        $stopwatch = $prop->getValue($listener);

        $this->assertFalse($stopwatch->isStarted('check_command'));
    }

    public function testOnTerminateCheck()
    {
        $command = $this->prophesize('Symfony\Component\Console\Command\Command');
        $command->getName()->shouldBeCalled()->willReturn('check');
        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $output->writeln(Argument::type('string'))->shouldBeCalled();
        $commandEvent = $this->prophesize('Symfony\Component\Console\Event\ConsoleCommandEvent');
        $commandEvent->getCommand()->shouldBeCalled()->willReturn($command->reveal());
        $terminateEvent = $this->prophesize('Symfony\Component\Console\Event\ConsoleTerminateEvent');
        $terminateEvent->getCommand()->shouldBeCalled()->willReturn($command->reveal());
        $terminateEvent->getOutput()->shouldBeCalled()->willReturn($output->reveal());

        $listener = new CommandListener();
        $listener->onConsoleCommand($commandEvent->reveal());
        $listener->onConsoleTerminate($terminateEvent->reveal());
    }
}
