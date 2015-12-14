<?php
namespace LiteTest;
use LiteTest;

abstract class TestRunner
{
	public $test_cases = array();
	public $test_results = array();

	public $output_verbose;
	public $output_debug;


	const PASS = "PASS";
	const FAIL = "FAIL";

	public function setOutputVerbose($onOff)
	{
		$this->output_verbose = $onOff;
	}	

	public function setOutputDebug($onOff)
	{
		$this->output_debug = $onOff;		
	}
	
	public function add_test_case(TestCase $test_case)
	{
		$test_name = get_class($test_case);
		$this->test_cases[$test_name] = $test_case;
	}
	
	public function get_test_cases()
	{
		return $this->test_cases;
	}
	
	public function run()
	{
		foreach($this->test_cases as $case_name => $case)
		{
			$this->test_results[$case_name] = $case->run($case_name);
		}
	}
	
	public function get_results()
	{
		return $this->test_results;
	}
}