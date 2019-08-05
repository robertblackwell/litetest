<?php
namespace LiteTest\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
	protected function configure()
	{
		// parent::configure();
		$this
			->setName('init')
			->setDescription('Initialize a litetest test directory')
			->addArgument(
				'name', 
				InputArgument::REQUIRED, 
				'The name of the test duirectory'
				)
			->addOption(
				'config', "c", 
				InputOption::VALUE_REQUIRED, 
				'Path to config or version file, defaults to src/version.json'
			)
			->setHelp(
				"Initialize a litetest test directory\n"
			);
	}
	/**
	* @param InputInterface  $input  Input object.
	* @param OutputInterface $output Output object.
	* @return integer
	*/
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("NO IMPLEMENTED");
		return -1;
	}
}
