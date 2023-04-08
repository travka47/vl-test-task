<?php
namespace Console\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;


class CountCommand extends Command
{
    public function configure() : void
    {
        $this
            ->setName('count')
            ->setDescription('Count values in .count files');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('sum: ' . $this->count());
        return self::SUCCESS;
    }

    private function count() : float
    {
        $rootPath = __DIR__ . '/../..';

        $finder = new Finder();
        $finder->files()->in($rootPath)->exclude('vendor')->name('count');

        $result = 0;

        foreach ($finder as $file) {
            $content = $file->getContents();
            $result += is_numeric($content) ? $content : 0;
            echo $file->getRelativePathname() . ' ' . $content . '  ';
        }

        return $result;
    }
}