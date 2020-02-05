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
    protected static $defaultName = 'app:parse-files';

    protected function configure()
    {
        $this->setDescription('Parse file(s) for dependencies.');
        $this->addOption(
            'dir',
            null,
            InputOption::VALUE_REQUIRED,
            'Which directory to search for files to parse for dependencies',
            ".\public\\files"
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
                $contents = $file->getContents();
                $extension = $file->getExtension();
                $fileName = $file->getRelativePathName();
                if ($extension == "json") {
                    $rows = $this->get_json_dependencies($contents, $rows);
                    $table->setHeaderTitle($fileName);
                } else {
                    $rows = $this->get_lock_dependencies($contents, $rows);
                    $table->setHeaderTitle($fileName);
                }
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

    public function get_json_dependencies($fileContent, $rows)
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

    public function get_lock_dependencies($fileContent, $rows)
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
