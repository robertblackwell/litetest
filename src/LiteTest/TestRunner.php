<?php
namespace LiteTest;

use LiteTest;

abstract class TestRunner
{
	public $test_cases = [];
	public $test_results = array();

	public $output_verbose;
	public $output_debug;


	const PASS = "PASS";
	const FAIL = "FAIL";

	public function __construct()
	{
	}

	/**
	 * @param mixed $onOff Set verbose output to this state.
	 * @return TestRunner
	 */
	public function setOutputVerbose($onOff) : TestRunner
	{
		if (is_null($onOff)) {
			$this->output_verbose = false;
		} elseif (is_bool($onOff)) {
			$this->output_verbose = $onOff;
		} else {
			throw new \Exception("onOff must be bool or null");
		}
		return $this;
	}

	/**
	 * @param mixed $onOff Set denug output to this state.
	 * @return TestRunner
	 */
	public function setOutputDebug($onOff) : TestRunner
	{
		if (is_null($onOff)) {
			$this->output_debug = false;
		} elseif (is_bool($onOff)) {
			$this->output_debug = $onOff;
		} else {
			throw new \Exception("onOff must be bool or null");
		}
		return $this;
	}
	
	/**
	 * @param TestCase $test_case test case to add to runner.
	 * @return void
	 */
	public function add_test_case(TestCase $test_case) : void
	{
		$test_name = get_class($test_case);
		$this->test_cases[$test_name] = $test_case;
	}

	/** @return array */
	public function get_test_cases() : array
	{
		return $this->test_cases;
	}
	
	/** @return void */
	public function run() : void
	{
		foreach ($this->test_cases as $case_name => $case) {
			$this->test_results[$case_name] = $case->run();
		}
	}
	/** @return array */
	public function get_results() : array
	{
		return $this->test_results;
	}
}
