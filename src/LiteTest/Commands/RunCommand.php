<?php
namespace LiteTest\Commands;

use LiteTest\Actions\Run;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
	protected function configure()
	{
		// parent::configure();
		$this
			->setName('run')
			->setDescription('Run test cases')
			->addArgument( 
				'testcases',
				InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
				'List of test cases to run'
			)->addOption(
				Run::CONFIG_FILE,
				"c",
				InputOption::VALUE_REQUIRED,
				'Path to config file defaults to litetest.config.php'
			)->addOption(
				Run::BOOTSTRAP_FILE,
				"b",
				InputOption::VALUE_REQUIRED,
				'Path to boostrap file defaults to bootstrap.php file'
			)->addOption(
				'debug',
				"d",
				InputOption::VALUE_NONE,
				'Turns on debugging output'
			)
			->setHelp(
				"Run a list of test cases. Testcases may be on command line or in bootstrap file.\n"
			);
	}
	/**
	* @param InputInterface  $input  Input object.
	* @param OutputInterface $output Output object.
	* @return integer
	*/
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$action = new \LiteTest\Actions\Run();
		$action->execute($input, $output);


	}
}
