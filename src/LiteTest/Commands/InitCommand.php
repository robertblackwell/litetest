<?php
namespace LiteTest\Commands;

use Release\SymfonyCommands\BaseCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends BaseCommand
{
	protected function configure()
	{
		// parent::configure();
		$this
			->setName('init')
			->setDescription('Initialize a version file')
			->addArgument(
				'version', 
				InputArgument::REQUIRED, 
				'The initial version such as v1.0.3 NO prereease or build meta data'
				)
			->addOption(
				'config', "c", 
				InputOption::VALUE_REQUIRED, 
				'Path to config or version file, defaults to src/version.json'
			)
			->setHelp(
				"Initialize a version file with a starting version. \n"
				."Default location for version file is src/version.json \n"
				."If ./src dir does not exist default location is ./version.json\n"
				."If --config option is used command DOES NOT create intermediate directories\n"
			);
	}
	/**
	* @param InputInterface  $input  Input object.
	* @param OutputInterface $output Output object.
	* @return integer
	*/
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		try {
			$parameters = new \Release\Parameters($input);
			$executor = new \Release\Executor\Init();
			if ($executor->exec($parameters)) {
				$output->writeln("verify successful");
			} else {
				throw new \Exception("verified failed check version file against git tags, git describe");
			}
			return 0;
		} catch (\Exception $e) {
			$output->writeln("command failed " . $e->getMessage());
			return -1;
		}
	}
}
