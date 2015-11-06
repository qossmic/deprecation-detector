<?php

namespace Tests\SensioLabs\DeprecationDetector\Console\Command\CheckCommand;

use SensioLabs\DeprecationDetector\Console\Command\CheckCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CheckCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CommandTester
     */
    private $commandTester;

    /**
     * @var Command
     */
    private $command;

    public function setUp()
    {
        $application = new \SensioLabs\DeprecationDetector\Console\Application();
        $application->add(new CheckCommand());

        $this->command = $application->find('check');
        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @param $sourcePath
     * @param $rulesetPath
     * @param $mentionedArgument
     *
     * @dataProvider invalidPaths
     */
    public function testCommandThrowsHelpfulExceptionWithInvalidPaths($sourcePath, $rulesetPath, $mentionedArgument)
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            sprintf(
                '%s argument is invalid: "%s" is not a path.',
                $mentionedArgument,
                ('Rule set' === $mentionedArgument ? $rulesetPath : $sourcePath)
            )
        );

        $this->executeCommand($sourcePath, $rulesetPath);
    }

    /**
     * returns invalid sourcePath and rulesetPath pairs, together with the argument,
     * that should be mentioned in the exception message.
     *
     * @return array
     */
    public function invalidPaths()
    {
        return [
            ['doesnotexist', 'doesnotexist', 'Source directory'], // both are invalid
            ['examples', 'doesnotexist', 'Rule set'],             // ruleset is invalid
            ['doesnotexist', 'examples', 'Source directory'],     // source is invalid
        ];
    }

    public function testCommandWithExampleCodeWorks()
    {
        $this->executeCommand('examples', 'examples');

        $this->assertEquals(0, $this->commandTester->getStatusCode());

        $display = $this->commandTester->getDisplay();

        $this->assertRegExp('/27 deprecations found/', $display);

//        echo $display;
    }

    public function testCommandWithFailOption()
    {
        $this->executeCommand('examples', 'examples', ['--fail' => true]);

        $this->assertGreaterThan(0, $this->commandTester->getStatusCode());
    }

    /**
     * Helper method for simplified executing of CheckCommand
     *
     * @param string $sourcePath     source argument
     * @param string $rulesetPath    ruleset argument
     * @param array $options Further options as key => value array
     */
    private function executeCommand($sourcePath, $rulesetPath, $options = array())
    {
        $arguments = array_merge(
            $options,
            array(
                'command' => $this->command->getName(),
                'source' => $sourcePath,
                'ruleset' => $rulesetPath,
                '--no-cache' => true,
            )
        );

        $this->commandTester->execute($arguments);
    }
}
