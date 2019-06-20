<?php
class TestTestRunnerCLI extends LiteTest\TestCase 
{	
	function test_prints_results()
	{
		$CLI_runner = new LiteTest\TestRunnerCLI();
		$CLI_runner->time_precision = 0;
		$CLI_runner->add_test_case(new TestingTestCase());

		ob_start();
		$CLI_runner->print_results();
		$result = ob_get_contents();
		ob_end_clean();

		$prove = explode("\n", $result);
		$newProve = [];
		foreach($prove as $line) {
			if ($line != "")
				$newProve[] = $line;
		}

		$clear_screen = urldecode("%1B%5BH%1B%5B2J");
		//$this->assert_equals($clear_screen, $prove[0]);
		$c = new Colors\Color();
		$failed_test =  
			"["
			. $c("FAIL")->red()
			. $c("]")->reset()
			. $c(" [0 ms] [TestingTestCase] ")->reset()
			. $c("test_one")->red()
			// . $c(" line 9")->reset()
			;

		// $failed_test = "[\x1b[0;31mFAIL\x1b[m] [0 ms] [TestingTestCase] \x1b[0;31mtest_one\x1b[m line 9";
		$this->assert_equals($failed_test, $newProve[0]);

		// $passed_test = "[\x1b\[0;32mPASS\x1b\[m] [0 ms] [TestingTestCase] test_two";
		// $passed_test = "\[\\x1b\[0;32mPASS\\x1b\[m\] \[0 ms\] \[TestingTestCase\] test_two";
		$passed_test = 
			"["
			.$c("PASS")->green()->bold()
			.$c("]")->reset()
			.$c(" [0 ms] [TestingTestCase] test_two")->reset();

		$this->assert_true(in_array($passed_test, $prove));

		$summary = "Cases: 1  Passed tests: 2  Failed tests: 1  Total assertions: 3  Running time: 0 ms";
		
		$this->assert_true(preg_match("/".$summary."/", $result) == 1);
	}
	
	function xtest_if_case_provided_in_construct_autoruns()
	{
		ob_start();
			$CLI_runner = new LiteTest\TestRunnerCLI(new TestingTestCase());
			$result = ob_get_contents();
		ob_end_clean();
		
		
		$test_results = $CLI_runner->get_results();
		$this->assert_equals(2, sizeof($test_results["TestingTestCase"]));
	}
}