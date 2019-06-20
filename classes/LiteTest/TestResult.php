<?php

namespace LiteTest;
/**
 * Holds the result for the execution of a single testcase
 * and all the tests inside that testcase
 */
class TestResult 
{
	const RESULT = "result";
	const EXCEPTION = "exception";
	
	public $name;
	public $assertions = array();
	public $assertion_frames;
	public $backtraces = [];
	public $error_line;
	public $running_time = 0;
	public $testcase;
	
	public function __construct($name)
	{
		$this->set_name($name);
		$this->assertions = [];
		$this->assertion_frames = [];
	}
	
	public function get_name()
	{
		return $this->name;
	}
	
	public function set_name($name)
	{
		$this->name = $name;
	}
	
	public function get_testcase()
	{
		return $this->testcase;
	}

	public function set_testcase($testcase)
	{
		$this->testcase = $testcase;
	}
	public function add_assertion_frame($frame)
	{
		$this->assertion_frames[] = $frame;
	}
	public function add_assertion($result, \Exception $exception = null, $line_number=-1, $file_name="")
	{

		// $msg = ($exception === null )? "NULL" : $exception->getMessage();
		// print __METHOD__."$result, $msg,  $line_number\n";
		$this->assertions[] = new Assertion($result, $exception, $line_number, $file_name);
		// $this->assertions[] = array(self::RESULT => $result, self::EXCEPTION => $exception);
	}

	public function count_assertions()
	{
		return sizeof($this->assertions);
	}
	
	public function passed()
	{
		foreach($this->assertions as $index => $one_assertion)
		{
			if($test_failed = !$one_assertion->result) return false;
		}
		
		return true;
	}
	
	public function get_exception()
	{
		// throw new \Exception("deprecated");
		
		foreach($this->assertions as $index => $one_assertion)
		{
			$x = $one_assertion->result;
			$test_failed = !$one_assertion->result;
			if($test_failed) 
				return $one_assertion->exception;
		}
	}
	
	public function set_error_line($line)
	{
		$this->error_line = $line;
	}
	
	public function get_error_line()
	{
		return $this->error_line;
	}
	
	public function set_running_time($time)
	{
		$this->running_time = $time;
	}
	
	public function get_running_time()
	{
		return $this->running_time;
	}
}