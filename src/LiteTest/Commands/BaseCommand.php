<?php
namespace LiteTest\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommand extends \Symfony\Component\Console\Command\Command
{
	const BUMP_MAJOR = "major";
	const BUMP_MINOR = "minor";
	const BUMP_PATCH = "patch";
	
	public $entity;
	public $action;

	/**
	*
	* @return void
	*/
	protected function configure()
	{
		$this->addOption('relmeta', "r", InputOption::VALUE_REQUIRED, 'Prelrease meta data');
		$this->addOption('buildmeta', "b", InputOption::VALUE_REQUIRED, 'Build meta data');
		$this->addOption('config', "c", InputOption::VALUE_REQUIRED, 'Path to config or version file');

		$this->shared_definition =  array(
				new InputOption(
					'relmeta', 
					"r", InputOption::VALUE_REQUIRED, 
					'Prelrease meta data'),
				new InputOption(
					'buildmeta', 
					"b", InputOption::VALUE_REQUIRED, 
					'Build meta data'),
				new InputOption(
					'config', 
					"c", InputOption::VALUE_REQUIRED, 
					'Path to config or version file'),
			);
	}

}
