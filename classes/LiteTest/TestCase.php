<?php

namespace LiteTest;

class TestCase
{
	const TEST_METHOD_PREFIX = "test_";
	//
	// Beware - cannot put _test at the end of a test function name because other
	// methods in TestCase class have that in their name
	//
	const TEST_METHOD_REGEX = "/^test/i";

	public $output_verbose;
	public $output_debug;

	protected $temporal_result;
	protected $backtraces;
	
	function setOutputVerbose($onOff)
	{
		$this->output_verbose = $onOff;
	}
	function setOutputDebug($onOff)
	{
		$this->output_debug = $onOff;
	}

	public function pass()
	{
		if($this->output_verbose) print ".";

		$this->temporal_result->add_assertion(true);
		return true;
	}
	
	public function fail($message)
	{
		if( $this->output_verbose) print "F";

		$exception = new \Exception($message);
		$this->store_exception($exception);
		return false;
	}
	
	protected function store_exception(\Exception $exception)
	{

		list($error_line, $error_file) = $this->find_error_line($exception);

		// print __METHOD__." line : $error_line file : $error_file \n";
		
		$this->temporal_result->add_assertion(false, $exception, $error_line, $error_file);
	}
	
	protected function find_error_line(\Exception $exception)
	{
		$trace = $exception->getTrace();
		$line = "Unknown line";
		$case_name = get_class($this);
		$file = "";

		foreach($trace as $execution_point)
		{

			$assert_funcs =[
				'assert_true', 
				'assert_false', 
				'assert_equal', 
				'assertEqual', 
				'assertEquals',
				'asserTrue',
				'assertNotNull'
			];
			$file = "";
			$function = $execution_point['function'];
			if( in_array($function, $assert_funcs )){
// 				print __METHOD__."FOUND IT\n";
// 				print __METHOD__." function : ". $execution_point['function'] ."\n";
// 				print __METHOD__." file : ". $execution_point['file'] ."\n";
// 				print __METHOD__." line : ". $execution_point['line'] ."\n";
				$file = $execution_point['file'];
				$line = $execution_point['line'];
				break;
			} 
// 			if(isset($execution_point["file"]))
// 			{
// 				$path_info = pathinfo($execution_point["file"]);
// 				$file = $path_info["filename"];
// 			}
// 			
// 			if($file == $case_name)
// 			{
// 				$line = $execution_point["line"];
// 				break;
// 			}
		}
		// print __METHOD__." line : $line  file: $file \n";
		return [$line,$file]; 

		// $this->temporal_result->set_error_line($line);
	}
	public function assertNotNull($prove)
	{
		if($prove === null) return $this->pass();
			
		return $this->fail("Failed asserting true for {$this->variable_dump($prove)}");
	}		
	public function assertTrue($prove)
	{
		if($prove === true) return $this->pass();
			
		return $this->fail("Failed asserting true for {$this->variable_dump($prove)}");
	}	
	public function assertFalse($prove)
	{
		if($prove === false) return $this->pass();
			
		return $this->fail("Failed asserting true for {$this->variable_dump($prove)}");
	}	
	public function assertEqual($expected, $prove) 
	{
		$fail_message = "Failed asserting that expected:\n".$this->variable_dump($expected)
					."\nequals given:\n".$this->variable_dump($prove);
					
		if((is_bool($prove) OR is_bool($expected)) AND ($expected !== $prove))
			return $this->fail($fail_message);
			
		if($expected == $prove) return $this->pass();
		
		return $this->fail($fail_message);	
	}

	public function assertEquals($expected, $prove) 
	{
		$fail_message = "Failed asserting that expected:\n".$this->variable_dump($expected)
					."\nequals given:\n".$this->variable_dump($prove);
					
		if((is_bool($prove) OR is_bool($expected)) AND ($expected !== $prove))
			return $this->fail($fail_message);
			
		if($expected == $prove) return $this->pass();
		
		return $this->fail($fail_message);	
	}

	public function assert_true($prove)
	{
		if($prove === true) return $this->pass();
			
		return $this->fail("Failed asserting true for {$this->variable_dump($prove)}");
	}
	
	public function assert_false($prove)
	{
		if($prove === false) return $this->pass();
			
		return $this->fail("Failed asserting false for {$this->variable_dump($prove)}");
	}
	
	public function assert_equals($expected, $prove) 
	{
		$fail_message = "Failed asserting that expected:\n".$this->variable_dump($expected)
					."\nequals given:\n".$this->variable_dump($prove);
					
		if((is_bool($prove) OR is_bool($expected)) AND ($expected !== $prove))
			return $this->fail($fail_message);
			
		if($expected == $prove) return $this->pass();
		
		return $this->fail($fail_message);	
	}
	
	//
	// @TODO - upgrade so that only methods from derived classes
	//			are considered as test cases. This will allow
	//			more flexibility in the naming of testcases.
	// WE can be a little smarter here than the original 
	// Only test the name against the defined patter 
	// If the method was actually "declared" in th testcase class
	//
	public function get_tests() 
	{	
		$reflected_self = new \ReflectionClass($this);
		
		$tests = array();
		foreach($reflected_self->getMethods() as $one_method) 
		{	

			$my_klass_name = get_class($this);
			$declaring_class_name = $one_method->getDeclaringClass()->getName();

			//print "this_name : $my_klass_name declaring_class : {$declaring_class_name} {$one_method->name} \n";
			// $reflected_method = new \ReflectionMethod(get_class($this), $one_method);
			// $declaring_klass = $reflected_method->getDeclaringClass()->getName();
			// print "method: $one_method declared in: $declaring_klass\n";
			//continue;

			$method_name = $one_method->name;
			// if( $my_klass_name === $declaring_class_name)
			if($this->is_test($method_name)) $tests[] = $method_name;
		}
		
		return $tests;
	}
	
	protected function is_test($method_name)
	{
		return (preg_match(self::TEST_METHOD_REGEX, $method_name) == 1);
	}
	
	public function run($testcase)
	{
		$results = array();
		
		foreach($this->get_tests() as $one_test)
		{
			$results[$one_test] = $this->run_one($one_test, $testcase);
		}

		return $results;
	}
	
	public function run_one($test_name, $testcase)
	{
		// throw new \Exception("got here");
		$this->temporal_result = new TestResult($test_name);
		$this->temporal_result->set_testcase($testcase);
		
		try
		{
			print "\n{$testcase}::{$test_name} \n";
			$start_time = microtime(true);
			$this->before_each();
			$this->$test_name();
		}
		catch(Exception $exception)
		{
			$this->store_exception($exception);
		}

		$this->after_each();
		$result_time = microtime(true) - $start_time;
		$this->temporal_result->set_running_time($result_time);		
		// $r = ($this->temporal_result->passed())? "P":"F";
		// print " $testcase::$test_name ". ($r)."\n";
		return $this->temporal_result;
	}
	
	public function before_each(){}
	public function after_each(){}
	
	protected function variable_dump($subject)
	{
		ob_start();
			var_dump($subject);
			$result = ob_get_contents();
		ob_end_clean();
		
		return $result;
	}
}