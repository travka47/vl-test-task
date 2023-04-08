<?php
namespace Console\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;


class CountCommand extends Command
{
    protected function configure() : void
    {
        $this
            ->setName('count')
            ->setDescription('Count values in .count files');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('sum: ' . $this->count());
        return self::SUCCESS;
    }

    private function count() : float
    {
        $rootPath = __DIR__ . '/../..';
        $regex = '/^-?\d+(\.\d+)?$/';
        $split = "/[\s,]+/";

        $finder = new Finder();
        $finder->files()->in($rootPath)->exclude('vendor')->name('count');

        $result = 0;

        foreach ($finder as $file) {
            $content = $file->getContents();

            $numbers = preg_grep($regex, preg_split($split, $content));
            $result += array_sum($numbers);

//            echo $file->getRelativePathname() . ' ' . array_sum($numbers) . '  ';
        }

        return $result;
    }
}