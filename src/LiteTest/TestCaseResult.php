<?php

namespace LiteTest;

/**
 * Holds the result of a run of a testcase.
 * Essentially a list of test results for the given testcase
 *
 */
class xTestCaseResult
{
	public $testcase_name;
	public $testcase_object;
	public $test_results;
	
	/**
	* @param TestCase $testcase_object THe TestCase for which this instasnce is a result.
	* @return TestCaseResult
	*
	*/
	public function __construct(TestCase $testcase_object)
	{
		$this->testcase_object = $testcase_object;
		$this->testcase_name = get_class($testcase_object);
		$this->test_results = [];
	}
	/**
	* Adds a test result to this testcase result
	* @param Testresult $result The test result to be added to the case.
	* @return void
	*/
	public function add_test_result(Testresult $result)
	{
		$this->test_results[] = $result;
	}
	/**
	* Get an array bof testresults from this thiscase result set.
	* @return array
	*/
	public function get_test_results() : array
	{
		return $this->test_results;
	}
}
