<?php
namespace LiteTest;

use LiteTest;

use Colors\Color;

class TestRunnerCLI extends TestRunner
{
	const RED_TEXT = "\x1b[0;31m";
	const GREEN_TEXT = "\x1b[0;32m";
	const WHITE_TEXT = "\x1b[m";
	
	protected $total_results = 0;
	protected $total_failed = 0;
	protected $total_assertions = 0;
	protected $tests_running_time = 0;
	
	public $time_precision = 6;
	
	public function __construct($test_case = null)
	{
		if (!empty($test_case) && ($test_case instanceof TestCase)) {
			$this->add_test_case($test_case);
			$this->print_results();
		}
	}
	
	public function print_results()
	{
		// system("clear");

		$this->run();

		echo PHP_EOL;

		foreach ($this->get_results() as $case_name => $test_results) {
			$this->print_case_results($case_name, $test_results);
		}
		
		$this->print_summary();
	}
	
	protected function print_summary()
	{
		echo PHP_EOL;
		echo "Cases: " . sizeof($this->test_cases) . "  ";
		echo "Passed tests: " . ($this->total_results + $this->total_failed) ."  ";
		echo "Failed tests: " . $this->total_failed ."  ";
		echo "Total assertions: " . $this->total_assertions ."  ";
		echo "Running time: " . $this->format_time($this->tests_running_time) . " ms";
		// echo PHP_EOL;
	}
	
	protected function print_case_results($case_name, $test_results)
	{
		foreach ($test_results as $one_result) {
			$this->total_assertions += $one_result->count_assertions();
			// foreach($one_result->assertions as $assertion){
			// 	print "assertion: ".$assertion->result_string."\n";
			// }
			if ($one_result->passed()) {
				$this->total_results++;
				$this->print_test_pass($one_result);
			} else {
				$this->total_failed++;
				$this->print_test_fail($one_result);
			}
		}
	}

	protected function print_test_pass(TestResult $result)
	{
		$case_name = $result->get_testcase();
		$running_time = $this->format_time($result->get_running_time());

		$c = new Color();

		echo
			PHP_EOL
			. $c("[")->reset()
			. $c(self::PASS)->green()->bold()
			. $c("] [{$running_time} ms] [$case_name] {$result->get_name()}")->reset();
	}
	
	protected function print_test_fail(TestResult $result)
	{
		$case_name = $result->get_testcase();
		$running_time = $this->format_time($result->get_running_time());

		$c = new Color();
		
		$line = PHP_EOL
			. $c("[")->reset()
			. $c(self::FAIL)->red()
			. $c("]")->reset()
			. $c(" [{$running_time} ms] [$case_name] ")->reset()
			. $c("{$result->get_name()}")->red()
			// . $c(" line {$result->get_error_line()}")->reset() - could be multiple assert fails
			. PHP_EOL.PHP_EOL;

		echo $line;
		foreach ($result->assertion_results as $result) {
			if (!$result->passed) {
				$frame = $result->frame->stack[0];
				unset($frame->object);
				echo $c("FAILED Assertion")->red()->bold();
				if (count($frame->args) == 2) {
					$arg0 = gettype($frame->args[0]) .":(" . var_export($frame->args[0], true) .")";
					$arg1 = gettype($frame->args[1]) .":(" . var_export($frame->args[1], true) .")";
					echo " {$frame->function}({$arg0},{$arg1}) FAIL ". PHP_EOL;
				} elseif (count($frame->args) == 1) {
					$arg0 = gettype($frame->args[0]) .":(" . var_export($frame->args[0], true).")";
					echo " {$frame->function}({$arg0}) FAIL ". PHP_EOL;
				} elseif (count($frame->args) == 0) {
					echo " {$frame->function}() FAIL ". PHP_EOL;
				} else {
					throw new \Exception("assert with wrong number");
				}
				echo "\tAT line: {$frame->line} file: {$frame->file} " . PHP_EOL;
				for ($i = 1; $i < count($result->frame->stack); $i++) {
					$caller = $result->frame->stack[$i];
					echo "\tCalled from {$caller->function} at line:{$caller->line} in file:{$caller->file} ". PHP_EOL;
				}
			}
		}
		return;
		foreach ($result->assertion_results as $assertion) {
			if (! $assertion->result) {
				$exception  = $assertion->exception;
				$prefix = $exception->getMessage();
				echo $exception->getMessage() . " AT ". $assertion->file_name ."]::" . $assertion->line_number."\n";
				if ($this->output_debug) {
					echo $exception->getTraceAsString().PHP_EOL;
				}
			}
		}
	}
	
	protected function format_time($time)
	{
		return number_format($time, $this->time_precision);
	}
}
