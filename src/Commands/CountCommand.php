<?php
namespace Console\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CountCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('count')
            ->setDescription('Count values in .count files');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('counter');
        return self::SUCCESS;
    }

}