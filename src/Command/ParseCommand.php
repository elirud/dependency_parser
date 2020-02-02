<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ParseCommand extends Command
{
    protected static $defaultName = 'app:parse-files';

    protected function configure()
    {
        $this->setDescription('Parse file(s) for dependencies.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $table = new Table($output);
        $table->setHeaders(['Product', 'Version']);
        $rows = array();
        $finder->files()->in(".\public\\files");

        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $contents = $file->getContents();
                $extension = $file->getExtension();
                $fileName = $file->getRelativePathName();
                if ($extension == "json") {
                    $rows = $this->get_json_dependencies($contents, $rows);
                    $table->setHeaderTitle('Dependencies found in '.$fileName);
                } else {
                    $dependencies = $this->get_lock_dependencies($contents);
                }
                $output->writeln($dependencies);
                $table->setRows($rows);
                $table->render();
            }
        }
        return 0;
    }

    public function get_json_dependencies($fileContent, $rows)
    {
        $contentAsJson = json_decode($fileContent, true);
        foreach ($contentAsJson["dependencies"] as $product => $version) {
            array_push($rows, new TableSeparator(), [$product, $version]);
        }
        return $rows;
    }

    public function get_lock_dependencies($fileContent)
    {
        return "lock";
    }
}
