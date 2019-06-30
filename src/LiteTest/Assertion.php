<?php
namespace LiteTest;

/**
* Represents the result of a single assertion within a test within a testcase
*/
class Assertion
{
	public $result;
	public $result_string;
	public $exception;
	public $line_number;
	public $file_name;
	/** 
	* @param boolean $result PassFail for the assertion.
	* @return Assertion
	*/
	function __construct(bool $result, $exception = null, $line_number = 0, $file_name = "")
	{
		$this->result = $result;
		$this->result_string = ($result) ? "PASS" : "FAIL";
		$this->exception = $exception;
		$this->line_number = $line_number;
		$this->file_name = $file_name;
		// print __METHOD__."file_name : {$this->file_name} \n";
	}
}
