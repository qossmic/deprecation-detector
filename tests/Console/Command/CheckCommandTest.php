<?php

namespace Tests\SensioLabs\DeprecationDetector\Console\Command\CheckCommand;

use SensioLabs\DeprecationDetector\Console\Command\CheckCommand;
use Symfony\Component\Console\Tester\CommandTester;

class CheckCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $sourcePath
     * @param $rulesetPath
     * @param $mentionedArgument
     *
     * @dataProvider invalidPaths
     */
    public function testCommandThrowsHelpfulExceptionWithInvalidPaths($sourcePath, $rulesetPath, $mentionedArgument)
    {
        $application = new \SensioLabs\DeprecationDetector\Console\Application();
        $application->add(new CheckCommand());

        $command = $application->find('check');
        $commandTester = new CommandTester($command);

        $this->setExpectedException(
            'InvalidArgumentException',
            sprintf(
                '%s argument is invalid: "%s" is not a path.',
                $mentionedArgument,
                ('Rule set' === $mentionedArgument ? $rulesetPath : $sourcePath)
            )
        );

        $commandTester->execute(
            array(
                'command' => $command->getName(),
                'source' => $sourcePath,
                'ruleset' => $rulesetPath,
                '--no-cache' => true,
            )
        );
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
}
