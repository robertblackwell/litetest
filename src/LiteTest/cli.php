<?php
// require_once dirname(dirname(__DIR__))."/vendor/autoload.php";
//
// This is a bit tricky - dont want to use composer autoload during the build of
// the phar as we need to use it when running tests
//
// require_once dirname(dirname(__DIR__))."/vendor/kevinlebrun/colors.php/src/Colors/Color.php";

interface iCliCommand
{
	public function execute($cli, array $options, array $arguments);
	public function version();
}

class OptionDefinition
{
	public $shortName;
	public $longName;
	public $valueRequired;
	public $description;
	public $isFile;
	public $key;
	public $path;
	function __construct()
	{
		$this->isFile = false;
	}
	function shortName($sname)
	{
		$this->shortName = $sname;
		return $this;
	}
	function longName($lname)
	{
		$this->longName = $lname;
		return $this;
	}
	function valueRequired($required)
	{
		$this->valueRequired = $required;
		return $this;
	}
	function description($description)
	{
		$this->description = $description;
		return $this;
	}
	function setIsFile()
	{
		$this->isFile = true;
		return $this;
	}
	function key($str)
	{
		$this->key = $str;
		return $this;
	}
}

class ParsedOption
{
	public $definition;
	public $file_path;
	public $key;
}

class CommandDefinition
{
	public $object;
	public $description;
	public $name;
	public $usage;
	public $help;
	function __construct()
	{
	}
	function description($desc)
	{
		$this->description = $desc;
		return $this;
	}
	function name($name)
	{
		$this->name = $name;
		return $this;
	}
	function usage($usage)
	{
		$this->usage = $usage;
		return $this;
	}
	function help($help)
	{
		$this->help = $help;
		return $this;
	}
}

class Cli
{
	//
	// An array of OptionDefinition objects
	//
	public $option_definitions;

	//
	// array of option value objects - thse rea the options actually provided
	//
	public $option_values;
	//
	// An object implementing  iCliCommand
	//
	public $command;
	/**
	 * @return Cli
	 */
	public function __construct()
	{
		$this->options = [];
		$this->addOption()->shortName("h")->longName("help")->description("command help")->valueRequired(false);
		$this->addOption()->shortName("V")->longName("version")->description("version number")->valueRequired(false);
	}
	/**
	* Adds an OptionDefinition to the Cli and allows chaining of the methods
	* of OptionDefinition
	* @return OptionDefinition
	*/
	public function addOption()
	{
		$opt = new OptionDefinition();
		$this->option_definitions[] = $opt;
		return $opt;
	}
	/**
	* creates a command definition and allows chaining of
	* the methods of CommandDefinition class
	* @param iCliCommand $cmd A command to add to the cli.
	* @return CommandDefinition
	*/
	function command(iCliCommand $cmd)
	{
		$this->command = new CommandDefinition();
		$this->command->object = $cmd;
		return $this->command;
	}

	/**
	* Returns the value of the option whose key is given, null if not exists
	* @param string $key The key for the option to be returned.
	* @return mixed
	*/
	public function getOptionValue(string $key)
	{
		if (isset($this->option_values[$key]))
			return $this->option_values[$key]->value;
		return null;
	}
	/**
	* Returns the file_path value of the option whose key is given, null if not exists
	*
	* throws an exception if this option is not set as a file type
	* @param string $key The key for which the value of a file path is returned.
	* @return string|null
	* @throws \Exception is option definition is not a file path
	*/
	public function getOptionFilePath(string $key) : ?string
	{
		if (isset($this->option_values[$key])) {
			if ($this->option_values[$key]->definition->isFile === false)
				throw new \Exception(" cannot get file_path for option: $key, not a file type option");
			return $this->option_values[$key]->file_path;
		}
		return null;
	}
	/**
	 * Get a keyed array of options
	 * @return array
	 * 
	 */
	public function getOptions()
	{
		$result = [];
		foreach ($this->option_values as $key => $v) {
			$result[$key] = $v;
		}
		return $result;
	}

	/**
	* Returns an array of the arguments that follow the options (program name NOT included)
	* @return array
	*
	*/
	public function getArguments()
	{
		return $this->arguments;
	}

	/**
	* Prints help text
	* @return void
	*/
	public function help()
	{
		$name = $this->command->name;
		$usage = $this->command->usage;
		
		
		$prefix_length = 0;
		$prefix_max_length = 0;
		$lines = [];

		foreach ($this->option_definitions as $def) {
			$prefix = "-".$def->shortName . "  --" . $def->longName;

			if (strlen($prefix) > $prefix_max_length)
				$prefix_max_length = strlen($prefix);

			$lines[] = ['prefix' => $prefix, "remainder" => $def->description];
		}

		$c = new Colors\Color();

		print $c($this->command->description)->cyan() . PHP_EOL;
		print $c("Usage: ")->reset()." ".$c($this->command->name)->green() ."  ". $c($usage)->white() . PHP_EOL;

		foreach ($lines as $line) {
			$prefix = str_pad($line['prefix'], $prefix_max_length);
			$description = $line['remainder'];

			print "\t".$c($prefix)->yellow() ."\t\t" . $c($description)->white() . PHP_EOL;
		}
	}
	/**
	* Prints version number
	* @return void
	*/
	public function version()
	{
		print $this->command->object->version()."\n";
	}
	/**
	* run the command
	* @param mixed $myargs Command line arguments argv in most cases.
	* @return void
	* exit with status code
	*/
	public function run($myargs)
	{
		try {
			$this->parse($myargs);
			$this->validateOptions();

			if (isset($this->parsed_options['h']) || isset($this->parsed_options['help'])) {
				$this->help();
				exit();
			}
			if (isset($this->parsed_options['V']) || isset($this->parsed_options['version'])) {
				$this->version();
				exit(0);
			}
			$this->command->object->execute($this, $this->parsed_options, $this->arguments);
		} catch (\Exception $e) {
			$c = new Colors\Color();
			print $c("ERROR: ")->red()->bold();
			print " ". $c($e->getMessage())->cyan()."\n";
			print $e->getTraceAsString();
		}
	}
	/**
	 * @param mixed $key Key value of option for which definition is required.
	 * @return mixed
	 * 
	 */
	private function getOptionDefinition($key)
	{
		//
		// key is either long or short name
		//
		if (strlen($key) == 1)
			return $this->isShortName($key);
		return $this->isLongName($key);
	}

	/**
	* breaks argv out into options, their values and arguments after the options
	* places the reult in this->parsed_options and $this->arguments
	* @param mixed $my_args Command line arguments, usually argv.
	* @return void
	*/
	private function parse($my_arg = null)
	{
		$cmd_args = [];
		$skip = [];
		$args = [];

		global $argv;
		$new_argv = is_null($my_arg) ? $argv : $my_arg;

		if (is_null($my_arg)) {
			array_shift($new_argv); // skip arg 0 which is the filename
		}

		foreach ($new_argv as $idx => $arg) {
			if (in_array($idx, $skip)) {
				continue;
			}
			$arg = preg_replace('#\s*\=\s*#si', '=', $arg);
			$arg = preg_replace('#(--+[\w-]+)\s+[^=]#si', '${1}=', $arg);

			if (substr($arg, 0, 2) == '--') {
				$eqPos = strpos($arg, '=');

				if ($eqPos === false) {
					$key = trim($arg, '- ');
					$opt_def = $this->getOptionDefinition($key);
					if ($opt_def === null)
						throw new \Exception("invalid option $key");
					//
					// Incase we have seen it before
					//
					$val = isset($cmd_args[$key]);

					// We handle case: --user-id 123 -> this is a long option with a value passed.
					// the actual value comes as the next element from the array.
					// We check if the next element from the array is not an option.
					//
					// However we must check to see if the option we are working on is:
					//	-	requires a value
					//	-	does not allow a value
					//	-	value is optional - we dont support this one
					//
					if ($opt_def->valueRequired) {
						if (isset($new_argv[ $idx + 1 ]) && ! preg_match('#^-#si', $new_argv[ $idx + 1 ])) {
							$cmd_args[$key] = trim($new_argv[ $idx + 1 ]);
							$skip[] = $idx;
							$skip[] = $idx + 1;
							continue;
						}
					}

					$cmd_args[$key] = $val;
				} else {
					$key = substr($arg, 2, $eqPos - 2);
					$cmd_args[$key] = substr($arg, $eqPos + 1);
				}
			} elseif (substr($arg, 0, 1) == '-') {
				if (substr($arg, 2, 1) == '=') {
					$key = substr($arg, 1, 1);
					$cmd_args[$key] = substr($arg, 3);
				} else {
					$chars = str_split(substr($arg, 1));

					foreach ($chars as $char) {
						$key = $char;
						$cmd_args[$key] = isset($cmd_args[$key]) ? $cmd_args[$key] : true;
					}
				}
			} else {
				// $cmd_args[] = $arg;
				$args[] = $arg;
			}
		}
		array_shift($args);//remove the program name from the $args array
		$this->arguments = $args;
		$this->parsed_options = $cmd_args;
	   // return [$cmd_args, $args];
	}

	/**
	* Returns OptionDefinition if the given string is a shortName for one of the defined options
	* false otherwise
	* @param string $name Name of option for which definition is required.
	* @return mixed
	*/
	private function isShortName($name)
	{
		foreach ($this->option_definitions as $option_def) {
			if ($name == $option_def->shortName)
				return $option_def;
		}
		return false;
	}

	/**
	* Returns OptionDefinition if the given string is a shortName for one of the defined options
	* false otherwise
	* @param string $name Name of option for which definition is required.
	* @return mixed
	*/
	private function isLongName($name)
	{
		foreach ($this->option_definitions as $option_def) {
			if ($name == $option_def->longName)
				return $option_def;
		}
		return false;
	}

	
	/**
	* Validates that the options that were parsed conform to the definitions,
	* 	-	they exist in the definition array
	*	-	have a value if required
	*  -   if should be a file we check the file exists using cwd as the base
	*	-	puts the relevant info in $this->options_value
	*
	* @return void
	* @throws \Exception If Invalid option found.
	*/
	private function validateOptions()
	{
		$option_values = [];
		foreach ($this->parsed_options as $opt => $value) {
			if ((false === ($opt_def = $this->isShortName($opt)) ) && ( false == ($opt_def = $this->isLongName($opt))))
				throw new \Exception(" $opt is invalid options");

			$optValue = new ParsedOption();
			$optValue->key = $opt_def->key;
			$optValue->definition = $opt_def;
			$optValue->value = $value;
			$option_values[$optValue->key] = $optValue;

			if ($opt_def !== false) {
				if ($opt_def->valueRequired && is_bool($value))
					throw new \Exception("option $opt requires value");
			}

			if ($opt_def !== false && $opt_def->isFile) {
				// print "testing file : $value \n";
				$path = getcwd()."/".$value;
				$info = new \SplFileInfo(getcwd()."/".$value);
				if (is_null($info) || ! $info->isFile())
					throw new \Exception("file $path does not exist");
				//$this->parsed_options[$opt] = $path;
				$optValue->file_path = $path;
			}
		}
		$this->option_values = $option_values;
	}
}


/**
 * Dummy implementatin of iCli inorder to do simple testing during development.
 * 
 */
class Dummy implements iCliCommand
{
	/**
	 * 
	 * @return string
	 */
	function version()
	{
		return "v0.0.1";
	}
	/**
	 * @param mixed $cli       Cli Instance to be executed.
	 * @param array $options   Array of options for this cli execution.
	 * @param array $arguments Array of argument values.
	 * @return void
	 */
	function execute($cli, array $options, array $arguments)
	{
		print_r($cli->getOptions());
		print_r($cli->getArguments());
		// print_r($cli);
		print("config-file value : [". $cli->getOptionValue('config-file') . "]\n");
		print("config-file file_path : [".$cli->getOptionFilePath('config-file')."]\n");
	}
}

/*

$cli = new Cli();
$cli->addOption()
	->shortName('c')->longname('config-file')
	->key("config-file")
	->valueRequired(true)
	->setIsFile()
	->description("json config file");

$cli->addOption()
	->shortName('b')->longname('bootstrap-file')
	->key('bootstrap-file')
	->valueRequired(true)
	->setIsFile()
	->description("php bootstrap file");

$cli->addOption()
	->shortName("v")->longName("verbose")
	->valueRequired(false)
	->key('verbose')
	->description("verbose")->valueRequired(false);

$cli->addOption()
	->shortName("d")->longName("debug")
	->valueRequired(false)
	->key('debug')
	->description("verbose")->valueRequired(false);

$cli->command(new Dummy())
	->name("LiteTest")
	->description("Run php test cases using LiteTest framework")
	->usage("[options] [arguments]")
	->help("Runs php test cases with LiteTest framework");

$cli->run($argv);

*/
