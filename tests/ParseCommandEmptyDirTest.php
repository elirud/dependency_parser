<?php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ParseCommandEmptyDirTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        // command to execute
        $command = $application->find('app:parse-files');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            // option 'dir' sent to command input
            '--dir' => '.\public\test\emptyDir'
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $this->assertContains('No files found in the ' .
                                '.\public\test\emptyDir directory.', $output);
    }
}
