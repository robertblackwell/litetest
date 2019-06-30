<?php
namespace LiteTest;

/**
* Represents the result of a single assertion within a test within a testcase
*/
class AssertionResult
{
	public $passed;
	public $frame;

	/**
	* @param boolean   $passed Did the assertion pass or fail.
	* @param \stdClass $frame  Stack frame for all methods in TestCase that called the assertion.
	* @return AssertionResult
	*/
	public function __construct(bool $passed, \stdClass $frame)
	{
		$this->passed = $passed;
		$this->frame = $frame;
	}
}
