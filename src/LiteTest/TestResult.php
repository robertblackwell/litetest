<?php

namespace LiteTest;

/**
 * Holds the result for the execution of a single test within a testcase
 * Holds all the result of ALL assertions called within that test.
 */
class TestResult
{
	
	public $test_name;
	public $assertion_frames;
	public $running_time = 0;
	public $testcase_object;
	public $testcase_name;
	
	/**
	*
	* @param TestCase $testcase_object The testCase from which this test arises.
	* @param string   $test_name       The name of the testcase method.
	* @return TestResult
	*/
	public function __construct(TestCase $testcase_object, string $test_name)
	{
		$this->test_name = $test_name;
		$this->test_results = [];
		$this->testcase_object = $testcase_object;
		$this->testcase_name = get_class($testcase_object);
	}
	/** @return string */
	public function get_name()
	{
		return $this->test_name;
	}
	/**
	* @param string $test_name Name of test method to be set.
	* @return void
	*/
	public function set_name(string $test_name) : void
	{
		$this->name = $test_name;
	}
	/**
	* Gets the class name of the testcase object.
	* @return string
	*/
	public function get_testcase() : string
	{
		return get_class($this->testcase_object);
	}
	/**
	* Get the testcase object for this result.
	* @return testCase
	*/
	public function get_testcase_object() : TestCase
	{
		return $this->testcase_object;
	}
	/**
	* Add the result for an assertion to this test result.
	* @param AssertionResult $assertion_result The assertion result to add.
	* @return void
	*/
	public function add_assertion_result(AssertionResult $assertion_result) : void
	{
		$this->assertion_results[] = $assertion_result;
	}
	/**
	* Returns the number of assertion results in this result.
	*  @return number
	*
	*/
	public function count_assertions()
	{
		return sizeof($this->assertion_results);
	}
	/**
	* Determines if all assertions in this test were passed.
	* @return boolean
	*/
	public function passed() : bool
	{
		foreach ($this->assertion_results as $index => $one_assertion) {
			if ($test_failed = !$one_assertion->passed) {
				return false;
			}
		}
		return true;
	}
	
	/**
	* @param number $time Test runtime.
	* @return void
	*/
	public function set_running_time($time) : void
	{
		$this->running_time = $time;
	}
	/**
	* Gets the runtime for this test.
	* @return number
	*
	*/
	public function get_running_time()
	{
		return $this->running_time;
	}
}
