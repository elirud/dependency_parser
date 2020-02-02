<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
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

        $finder->files()->in(".\public\\files");

        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $contents = $file->getContents();
                $extension = $file->getExtension();
                $output->writeln($file->getRelativePathName());
                if ($extension == "json") {
                    $dependencies = $this->get_json_dependencies($contents);
                } else {
                    $dependencies = $this->get_lock_dependencies($contents);
                }
                $output->writeln($dependencies);
            }
        }
        return 0;
    }

    public function get_json_dependencies($fileContent)
    {
        $contentAsJson = json_decode($fileContent, true);
        foreach ($contentAsJson["dependencies"] as $x => $x_value) {
            echo "Key=" . $x . ", Value=" . $x_value;
        }
        return "";
    }

    public function get_lock_dependencies($fileContent)
    {
        return "lock";
    }
}
