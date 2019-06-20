<?php

class TestTestCase extends LiteTest\TestCase 
{
	function assert_pass_fail($test_case, bool $pass_fail)
	{
		$dummy = end($test_case->temporal_result->assertion_frames);
		$d = $dummy->passed;
		assert($d === $pass_fail);
	}	
	function test_pass()
	{
		$test_case = new TestingTestCase();
		$test_case->temporal_result = new LiteTest\TestResult("some test");
		
		$test_case->pass();
		assert(end($test_case->temporal_result->assertion_frames)->passed === true);		

		assert(1 === $test_case->temporal_result->count_assertions());
	}
	
	function test_fail()
	{
		$test_case = new TestingTestCase();
		$test_case->temporal_result = new LiteTest\TestResult("some test");
		
		$test_case->fail("Failure message");
		assert(end($test_case->temporal_result->assertion_frames)->passed === false);		

		assert(1 === $test_case->temporal_result->count_assertions());
		
		assert("Failure message" === end($test_case->temporal_result->assertion_frames)->args[0]);
	}
	
	function test_assert_true()
	{
		$test_case = new TestingTestCase();
		$test_case->temporal_result = new LiteTest\TestResult("some test");
		
		$test_case->assert_true(true);
		$dummy = end($test_case->temporal_result->assertion_frames);
		$d = $dummy->passed;
		assert($d === true);

		$test_case->assert_true(false);
		$dummy = end($test_case->temporal_result->assertion_frames);
		$d = $dummy->passed;
		assert($d === false);
	}
	
	function test_assert_false()
	{
		$test_case = new TestingTestCase();
		$test_case->temporal_result = new LiteTest\TestResult("some test");
		
		$test_case->assert_false(false);
		$dummy = end($test_case->temporal_result->assertion_frames);
		$d = $dummy->passed;
		assert($d === true);
		
		$test_case->assert_false(true);
		$dummy = end($test_case->temporal_result->assertion_frames);
		$d = $dummy->passed;
		assert($d === false);
	}
	
	function test_assert_equals()
	{
		$test_case = new TestingTestCase();
		$test_case->temporal_result = new LiteTest\TestResult("some test");
		
		$test_case->assert_equals("value", "value");
		assert(end($test_case->temporal_result->assertion_frames)->passed === true);		

		$test_case->assert_equals("value 1", "value 2");
		assert(end($test_case->temporal_result->assertion_frames)->passed === false);		
		
		$test_case->assert_equals(true, 2);
		assert(end($test_case->temporal_result->assertion_frames)->passed === false);		
	}
	
	function test_exception_testing()
	{
		try 
		{
			throw new \Exception("some exception");
			$this->fail("Expected exception not thrown\n");
		}
		catch (\Exception $exception)
		{
			if($exception->getMessage() != "some exception")
				$this->fail("Expected exception not thrown\n");

			$this->pass();
		}
	}
	
	function test_test_list()
	{
		$test_case = new TestingTestCase();
		
		$test_list = $test_case->get_tests();
		$expected = array("test_one", "test_two");
		$this->assert_equals($expected, $test_list);
	}
	/** @todo Needs to be redone in light of use of assertion_frames */
	function test_run()
	{
		$test_case = new TestingTestCase();
		
		$results = $test_case->run("TestingTestCase");
	
		assert(is_array($results));
		assert("LiteTest\TestResult" === get_class($results["test_one"]));
		assert("LiteTest\TestResult" === get_class($results["test_two"]));
		assert("test_one" === $results["test_one"]->get_name());
		assert(2 === $results["test_one"]->count_assertions());
		$ln = $results["test_one"]->assertion_frames[1]->line;
		$f = $results["test_one"]->assertion_frames[1]->file;
		assert(9 === $results["test_one"]->assertion_frames[1]->line);
		assert($results["test_one"]->get_running_time() > 0.0);
 		assert($results["test_two"]->get_running_time() > 0.0);
	}
	
	function test_befor_each()
	{
		$test_case = new TestingTestCase();
		$test_case->run("TestingTestCase");
		$this->assert_equals(2, $test_case->before_each_call_count);
	}
	
	function test_after_each()
	{
		$test_case = new TestingTestCase();
		$test_case->run("TestingTestCase");
		$this->assert_equals(2, $test_case->after_each_call_count);
	}
}