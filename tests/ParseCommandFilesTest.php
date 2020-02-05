<?php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ParseCommandFilesTest extends KernelTestCase
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
            '--dir' => '.\public\test'
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        $this->assertContains("No dependencies found for " .
                                "emptyDependenciesGemfile.lock.", $output);
        $this->assertContains("No dependencies found for " .
                                "emptyDependenciesPackage.json.", $output);
        $this->assertContains("No dependencies found for " .
                                "noDependenciesGemfile.lock.", $output);
        $this->assertContains("No dependencies found for " .
                                "noDependenciesPackage.json.", $output);
    }
}
