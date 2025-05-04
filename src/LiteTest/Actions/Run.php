<?php
namespace LiteTest\Actions;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Run
{

	const CONFIG_FILE = "config-file";
	const BOOTSTRAP_FILE = "bootstrap-file";
	// const CONFIG_FILE_NAME= "litetest.json";
	// const CONFIG_FILE_NAME= "litetest.ini";
	const CONFIG_FILE_NAME= "litetest.config.php";
	const TEST_CLASS_REGEX="/(^Test|Test$)/i";

	public $input;
	public $output;
	public $args;
	public $options;
	public $cwd;
	public $tests;
	public $tests_relative_to;
	public $config;
	public $config_file_path;
	public $debug;
	public $bootstrap_file;
	public $verbose;

	/**
	 * @param $msg A text message to use when throwing a fatal error
	 * @return void Calls exit()
	 */
	public function throw_fatal_error($msg, $e = null)
	{
		print "XXERROR : $msg ".PHP_EOL.PHP_EOL;
		if (! is_null($e))
			print $e->getTraceAsString();
		exit();
	}

	/**
	* Get a list of php files that are to be loaded either from the config file
	* or as arguments to the command. Arguments take precedence
	* @return void
	*/
	public function setupListOfTests()
	{
		if ($this->debug) print __METHOD__."\n";
		//
		// check we have a valid config file or an argument that is a php file
		//
		if ($this->debug) print __METHOD__."no arguments - look to config file \n";
		
		if (null !== $this->input->getOption(self::CONFIG_FILE)) {
			$this->config_file_path = $this->input->getOptionFilePath(self::CONFIG_FILE_NAME);
		} else {
			$this->config_file_path = $this->cwd."/".self::CONFIG_FILE_NAME;
		}
		if ($this->debug) print __METHOD__."config file path {$this->config_file_path} \n";

		if (! file_exists($this->config_file_path)) {
			$this->throw_fatal_error("config file (". self::CONFIG_FILE .") required ");
		}

		$tmp_config = include $this->config_file_path;

		//
		// cast the config array to a stdClass object
		//
		$this->config = (object) $tmp_config;

		// $this->config = json_decode(json_encode($tmp_config), false);
		// $this->config = parse_ini_file($this->config_file_path, true);
		// $this->config = json_decode(file_get_contents($this->config_file_path));

		if (($this->config === null) || ($this->config === false)) {
			$this->throw_fatal_error("no content for config file, possible json error");
		}


		if ($this->debug) print __METHOD__."config should be loaded \n";

		if (count($this->args) == 0) {
			if (! isset($this->config->tests)) {
				$this->throw_fatal_error("no tests listed in config file");
			}
			$this->tests = $this->config->tests;
			$this->tests_relative_to = dirname($this->config_file_path);
		} else {
			if ($this->debug) print __METHOD__."arguments given - get tests from arguments \n";
			$this->tests = [];
			if (count($this->args) > 0)
				$this->tests = $this->args;

			$this->tests_relative_to = $this->cwd;
		}

		foreach ($this->tests as $file) {
			$file_path = $this->tests_relative_to ."/". $file;
			if ($this->debug) print "Testing existence of testcase : $file_path \n";
			if (! file_exists($file_path))
				$this->throw_fatal_error("file : $file_path does not exists");
		}
		if ($this->debug) print __METHOD__."\n";
	}
	/**
	* 	Gets the bootstrap file, from command options or from the config file
	*
	*  Command options take precedence.
	*	If no bootstrap command options, and no config file - no bootstrap file
	* @return void
	*/
	public function setupBootstrap()
	{
		if ($this->debug) 
			print __METHOD__."\n";
		if (null !== $this->input->getOption(self::BOOTSTRAP_FILE)) {
			if ($this->debug) print "bootstrap from options\n";
			$this->bootstrap_file = 
				$this->tests_relative_to 
				."/"
				. $this->input->getOption(self::BOOTSTRAP_FILE);

		} elseif (isset($this->config->bootstrap)) {
			if ($this->debug) print "bootstrap from config \n";
			$this->bootstrap_file = $this->cwd. "/". $this->config->bootstrap;
			if ($this->debug) print __METHOD__."[{$this->bootstrap_file}] \n";
		} else {
			if ($this->debug) print "no bootstrap \n";
			$this->bootstrap_file = null;
		}
		if ($this->debug) 
			print __METHOD__."\n";
	}
	/** @return void  */
	public function setup()
	{
		$this->setupListOfTests();
		$this->setupBootstrap();
	}

	/**
	 * @param mixed $input  InpuInterface.
	 * @param mixed $output OutputInterface.
	 * @return void
	 */
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$this->input = $input;
		$this->output = $output;
		$this->args = $input->getArgument('testcases');
		$this->cwd = getcwd();
		$x = $input->getOption('debug');
		$this->debug = $input->getOption('debug');
		$this->verbose = $output->isVerbose();

		$this->setup();
		if ($this->debug)
			$output->writeln(__METHOD__." after setup");

		//
		// Load the bootstrap file
		//
		$before_classes = get_declared_classes();

		// if( DEBUG ) print_r($this);
		try {
			if ($this->debug) print "Loading files ... \n";
			if (! is_null($this->bootstrap_file)) {
				if ($this->debug) print "Loading bootstrap : $this->bootstrap_file\n";
				include($this->bootstrap_file);
			}

			foreach ($this->tests as $test_file) {
				$fn = $this->tests_relative_to."/".$test_file;
				if ($this->debug) print "loading : $fn \n";
				error_reporting(-1);
				include $fn;
				if ($this->debug) print "loaded : $fn \n";
			}
		} catch (\Exception $e) {
			$this->throw_fatal_error($e->getMessage(), $e);
		}

		$testClasses = [];
		
		$after = array_diff(get_declared_classes(), [__CLASS__]);

		$classes_to_test = array_diff($after, $before_classes);

		foreach ($classes_to_test as $klass) {
			// if( preg_match("/^Test/", $klass) )
			if (preg_match(self::TEST_CLASS_REGEX, $klass)) {
				$testClasses[] = $klass;
				if ($this->debug) print " class : $klass added as test case \n";
			} else {
				if ($this->debug) print " class : $klass REJECTED as test case \n";
			}
		}

		$runner = (new \LiteTest\TestRunnerCLI())
			->setOutputDebug($this->debug)
			->setOutputVerbose($this->verbose);

		foreach ($testClasses as $suite) {
			$suite_obj = new $suite();

			$suite_obj->setOutputDebug($this->debug);
			$suite_obj->setOutputVerbose($this->verbose);

			$runner->add_test_case($suite_obj);
		}
		//
		// Test for empty set of tests
		//
		$cc = new \Colors\Color();
		if (count($testClasses) == 0) {
			echo $cc("WARNING ")->red()->bold();
			echo $cc("  No classes have been named to be test Suites (Test????) - ")->reset();
			echo $cc(" is this correct !!")->white()->bold();
			echo "\n";
		} else {
			foreach ($runner->get_test_cases() as $obj) {
				$arr = $obj->get_tests();
				if (count($arr) == 0) {
					echo  $cc("WARNING")->red()->bold();
					echo  $cc(" class ")->reset();
					echo  $cc(get_class($obj))->cyan()->bold();
					echo  $cc(" has no tests method (test_???) - ")->reset();
					echo  $cc(" is this correct ")->white()->bold();
					echo  "\n ";
				}
			}
		}
		$runner->print_results();
	}
}
