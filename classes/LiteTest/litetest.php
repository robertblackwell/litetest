<?php

//require_once dirname(dirname(__DIR__))."/vendor/autoload.php";

//
// This is a REALLY simple command line tool to run unit tests with LiteTest.
//
//  The basic requirements are:
//
//	-	it must take a list of test suite files via some form of config file 
//		(either named as a command option  or from a default location like ./LiteTest.js)
//
//	-	must be able to take a boostrap.php file on the command line so that test suite
//		can be run against multiple 'environments'. And in the absense of the command line
//		option the bootstrap must be deduced from the config file, so that
//
//	- 	individual test suites can be run by simply naming a php file as the ONLY argument or option
//	
//

require_once __DIR__."/cli.php";



class LiteTestCommand implements iCliCommand
{

	const CONFIG_FILE = "config-file";
	const BOOTSTRAP_FILE = "bootstrap-file";
	const CONFIG_FILE_NAME= "LiteTest.json";
	const TEST_CLASS_REGEX="/(^Test|Test$)/i";

	public $cli;
	public $args;
	public $options;
	public $cwd;
	public $tests_relative_to;
	public $config;
	public $config_file_path;
	public $debug;

	function throw_fatal_error($msg)
	{
		print "ERROR : $msg ".PHP_EOL.PHP_EOL;
		exit();
	}

	function version()
	{
		return "v.0.0.101";
	}

	//
	// Get a list of php files that are to be loaded either from the config file
	// or as arguments to the command. Arguments take precedence
	//
	function setupListOfTests()
	{
		if( $this->debug ) print __METHOD__."\n";
		//
		// check we have a valid config file or an argument that is a php file
		//
		if( $this->debug ) print __METHOD__."no arguments - look to config file \n";
		
		if( null !== $this->cli->getOptionValue(self::CONFIG_FILE) )
		{
			$this->config_file_path = $this->cli->getOptionFilePath(self::CONFIG_FILE_NAME);
		}
		else
		{
			$this->config_file_path = $this->cwd."/".self::CONFIG_FILE_NAME;
		}
		if( $this->debug ) print __METHOD__."config file path {$this->config_file_path} \n";

		if(! file_exists($this->config_file_path) )
		{
			$this->throw_fatal_error("config file (". self::CONFIG_FILE .") required ");
		}

		$this->config = json_decode(file_get_contents($this->config_file_path));

		if( $this->config === null )
		{
			$this->throw_fatal_error("no content for config file, possible json error");
		}


		if( $this->debug ) print __METHOD__."config should be loaded \n";

		if( count($this->args) == 0 )
		{
			if( ! isset($this->config->tests) )
			{
				$this->throw_fatal_error("no tests listed in config file");
			}
			$this->tests = $this->config->tests;
			$this->tests_relative_to = dirname($this->config_file_path);
		}
		else
		{
			if( $this->debug ) print __METHOD__."arguments given - get tests from arguments \n";
			$this->tests = [];
			if( count($this->args) > 0)
				$this->tests = $this->args;

			$this->tests_relative_to = $this->cwd;
		}

		foreach($this->tests as $file){
			$file_path = $this->tests_relative_to ."/". $file;
			if( $this->debug ) print "Testing existence of testcase : $file_path \n";
			if( ! file_exists($file_path) )
				$this->throw_fatal_error("file : $file_path does not exists");
		}
		if( $this->debug ) print __METHOD__."\n";
	}
	//
	// 	Gets the bootstrap file, from command options or from the config file
	//	
	//	Command options take precedence. 
	//	If no bootstrap command options, and no config file - no bootstrap file
	//
	function setupBootstrap()
	{

		if( $this->debug ) print __METHOD__."\n";
		if( null !== $this->cli->getOptionValue(self::BOOTSTRAP_FILE) )
		{
			if( $this->debug ) print "bootstrap from options\n";
			$this->bootstrap_file = $this->cli->getOptionFilePath('bootstrap-file');
		} 
		else if( isset($this->config->bootstrap) )
		{
			if( $this->debug ) print "bootstrap from config \n";
			$this->bootstrap_file = $this->cwd. "/". $this->config->bootstrap;
			if( $this->debug ) print __METHOD__."[{$this->bootstrap_file}] \n";
		}
		else
		{
			if( $this->debug ) print "no bootstrap \n";
			$this->bootstrap_file = null;
		}
		if( $this->debug ) print __METHOD__."\n";

	}

	function setup()
	{
		$this->setupListOfTests();
		$this->setupBootstrap();
	}

	function execute($cli, array $options, array $args)
	{
		$this->cli = $cli;
		$this->args = $cli->getArguments();
		$this->cwd = getcwd();
		$this->debug = false;

		$this->setup();
		if( $this->debug ) print __METHOD__." after setup \n";

		//
		// Load the bootstrap file
		//
		$before_classes = get_declared_classes();

		// if( DEBUG ) print_r($this);
		try
		{
			if( $this->debug ) print "Loading files ... \n";
			if( ! is_null($this->bootstrap_file) )
			{

				if( $this->debug ) print "Loading bootstrap : $this->bootstrap_file\n";
				require($this->bootstrap_file);
			}

			foreach($this->tests as $test_file)
			{
				if( $this->debug ) print "loading : $test_file \n";
				require_once($this->tests_relative_to."/".$test_file);
			}
		}
		catch(\Exception $e)
		{
			$this->throw_fatal_error($e->getMessage());
		}

		$testClasses = [];
		
		$after = array_diff(get_declared_classes(), [__CLASS__]);

		$classes_to_test = array_diff($after, $before_classes);


		foreach($classes_to_test as $klass)
		{
			// if( preg_match("/^Test/", $klass) )
			if( preg_match(self::TEST_CLASS_REGEX, $klass) )
				$testClasses[] = $klass;
		}

		$runner = new \LiteTest\TestRunnerCLI();

		foreach($testClasses as $suite)
		{
			$runner->add_test_case(new $suite());
		}

		$runner->print_results();
	}
}


$cli = new Cli();
$cli->addOption()
	->shortName('c')->longname(LiteTestCommand::CONFIG_FILE)
	->key(LiteTestCommand::CONFIG_FILE)
	->valueRequired(true)
	->setIsFile()
	->description("json config file");

$cli->addOption()
	->shortName('b')->longname(LiteTestCommand::BOOTSTRAP_FILE)
	->key(LiteTestCommand::BOOTSTRAP_FILE)
	->valueRequired(true)
	->setIsFile()
	->description("php bootstrap file");

$cli->addOption()
	->shortName("v")->longName("verbose")
	->key('verbose')
	->description("verbose")->valueRequired(false);

$cli->addOption()
	->shortName("d")->longName("debug")
	->key('debug')
	->description("debug")->valueRequired(false);

$cli->command(new LiteTestCommand())
	->name("LiteTest")
	->description("Run php test cases using LiteTest framework")
	->usage("[options] [arguments]")
	->help("Runs php test cases with LiteTest framework");

$cli->run($argv);

