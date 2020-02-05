<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\Console\Input\InputOption;
use function Symfony\Component\String\u;

class ParseCommand extends Command
{
    // the command you call from the CLI after 'php bin/console'
    protected static $defaultName = 'app:parse-files';

    protected function configure()
    {
        $this->setDescription('Parse file(s) for dependencies.');
        $this->addOption(
            'dir',   // option name, used to call with --dir
            null,
            InputOption::VALUE_REQUIRED,
            'Which directory to search for files to parse for dependencies',
            ".\public\\files" // default value
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $table = new Table($output);
        $table->setStyle('box-double');
        $table->setHeaders(['Product', 'Version']);
        $rows = array();
        $finder->files()->in($input->getOption('dir'));

        if ($finder->hasResults()) {
            $output->writeln("Dependencies found for:");
            foreach ($finder as $file) {
                // Get file information and contents
                $contents = $file->getContents();
                $extension = $file->getExtension();
                $fileName = $file->getRelativePathName();
                // Check file extension and call appropriate function.
                if ($extension == "json") {
                    $rows = $this->get_json_dependencies($contents, $rows);
                    $table->setHeaderTitle($fileName);
                } else {
                    $rows = $this->get_lock_dependencies($contents, $rows);
                    $table->setHeaderTitle($fileName);
                }
                // Render table if there are found dependencies.
                if (!empty($rows)) {
                    $table->setRows($rows);
                    $table->render();
                    $rows = [];
                } else {
                    $output->writeln("No dependencies found for "
                                    .$fileName . ".");
                }
            }
        } else {
            $output->writeln("No files found in the ".$input->getOption('dir').
                            ' directory.');
        }
        return 0;
    }

    /**
    * We get the dependencies from the json file by converting
    * the json string to an associative array, and get them by
    * checking the value for the key 'dependencies'. Here we assume
    * the json string contains a key 'dependencies' which in turn
    * points to an array with product name as key and version as value.
    *
    * @param $fileContent The content of the file to parse.
    * @param $rows The array where we put the rows for the eventual table.
    * @return $rows Array with the final rows for the eventual table.
    */
    private function get_json_dependencies($fileContent, $rows)
    {
        $contentAsJson = json_decode($fileContent, true);
        if (!isset($contentAsJson["dependencies"])) {
            return $rows;
        }
        foreach ($contentAsJson["dependencies"] as $product => $version) {
            array_push($rows, new TableSeparator(), [$product, $version]);
        }
        return $rows;
    }

    /**
    * We get the dependencies from the lock file through some simple
    * string operations using Symfonys UnicodeString. Here we assume
    * that 'DEPENDENCIES' and the next 'key' after are seperated by
    * a blankline (two newlines), and that the first whitespace for
    * a dependency is between the products name and its version.
    *
    * @param $fileContent The content of the file to parse.
    * @param $rows The array where we put the rows for the eventual table.
    * @return $rows Array with the final rows for the eventual table.
    */
    private function get_lock_dependencies($fileContent, $rows)
    {
        if (strpos($fileContent, 'DEPENDENCIES') == false) {
            return $rows;
        }

        $lockDependencies = u($fileContent)
                            ->after('DEPENDENCIES')->before("\n\n");

        if (u($lockDependencies)->collapseWhitespace()->isEmpty()) {
            return $rows;
        }
        foreach (u($lockDependencies)->split("\n") as $row) {
            array_push($rows, new TableSeparator(), u($row)
                                                    ->trimStart()
                                                    ->split(" ", 2));
        }
        return $rows;
    }
}
