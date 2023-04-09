<?php
namespace Console\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;


class CountCommand extends Command
{
    protected CONST ROOT_DIR = __DIR__ . '/../..';

    protected function configure() : void
    {
        $this
            ->setName('count')
            ->setDescription('Count values in count files')
            ->addOption('table', 't', null, 'Print result table')
            ->addArgument('directory', InputArgument::OPTIONAL, 'Change searching directory', self::ROOT_DIR);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $flagPrintTable = $input->getOption('table');

        if (is_dir($input->getArgument('directory'))) {
            $rootPath = $input->getArgument('directory');
        }
        else {
            $rootPath = self::ROOT_DIR;
            $output->writeln('<error>Unable directory, sum will be calculated for the root</error>');
        }

        $this->count($output, $flagPrintTable, $rootPath);

        return self::SUCCESS;
    }

    private function count(OutputInterface $output, $flagPrintTable, $rootPath) : void
    {
        $regex = '/^-?\d+(\.\d+)?$/';
        $split = "/[\s,]+/";

        $finder = new Finder();
        $finder->files()->in($rootPath)->name('count');

        $total = 0;
        $tableRows = [];

        foreach ($finder as $file) {
            $content = $file->getContents();

            $numbers = preg_grep($regex, preg_split($split, $content));
            $total += array_sum($numbers);

            $tableRows[] = [$file->getRelativePathname(), array_sum($numbers)];
        }

        if (empty($tableRows)) {
            $output->writeln('<fg=#8f43ee;bg=cyan>There are no count files in this directory</>');
            return;
        }

        if ($flagPrintTable) {
            $this->printTable($output, $rootPath, $tableRows, $total);
        }
        else {
            $output->writeln('<fg=#8f43ee>Total: </>' . $total);
        }
    }

    private function printTable(OutputInterface $output, $rootPath, $tableRows, $total): void
    {
        $tableRows[] = new TableSeparator();
        $tableRows[] = ['<fg=#8f43ee>Total</>', $total];

        $table = new Table($output);

        $table->setHeaderTitle($rootPath === self::ROOT_DIR ? 'Root directory' : $rootPath);
        $table->setHeaders(['File', 'Sum'])->setRows($tableRows);
        $table->setStyle('box');

        $table->render();
    }

}