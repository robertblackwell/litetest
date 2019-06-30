<?php

namespace LiteTest;

class TestCase
{
	const TEST_METHOD_PREFIX = "test_";
	//
	// Beware - cannot put _test at the end of a test function name because other
	// methods in TestCase class have that in their name
	//
	const TEST_METHOD_REGEX = "/^test/i";

	public $output_verbose;
	public $output_debug;
	
	/** @var AssertionResult $active_result */
	protected $active_result;

	public $results;
	
	public function __construct()
	{
		$this->results = [];
	}

	function setOutputVerbose($onOff)
	{
		$this->output_verbose = $onOff;
	}
	function setOutputDebug($onOff)
	{
		$this->output_debug = $onOff;
	}
	/**
	 */
	public function pass()
	{
		$frame = $this->assert_called_from();
		$this->record_pass($frame);
		if ($this->output_verbose) print ".";
		return true;
	}
	/**
	 *
	 */
	public function rec_fail($message)
	{
		if ($this->output_verbose) print "F";
		return false;
	}
	/**
	 * Record a successful assertion in the active TestResult instance
	 * @param mixed $assert_frame
	 * @return void
	 * @note Uses instance property $this->active_result which must be correctly
	 * managed by $this->run
	 */
	public function record_pass($assert_frame) : void
	{
		$assert_frame->passed = true;
		
		$tmp_assert_result = new AssertionResult(true, $assert_frame);
		$this->active_result->add_assertion_result($tmp_assert_result);
	}
	/**
	 * Record a failed assertion in the active TestResult instance
	 * @param mixed $assert_frame
	 * @return void
	 * @note Uses instance property $this->active_result which must be correctly
	 * managed by $this->run
	 */
	public function record_fail($assert_frame) : void
	{
		$assert_frame->passed = false;
		$this->active_result->add_assertion_result(new AssertionResult(false, $assert_frame));
	}
	/**
	 * Called by an assert method to capture the call stack of
	 * all previous callers inside this testcase downto but not including
	 * the assertXXXX method that was called.
	 * @return \stdClass Contains the abbreviated call stack
	 */
	public function assert_called_from() : \stdClass
	{
		$bt = debug_backtrace();
		if (false) {
			$frame = (object)$bt[1];
			return $frame;
		} else {
			$stack = [];
			// $stack[] = (object)$bt[1];
			for ($i = 1; $i < count($bt); $i++) {
				$f = $bt[$i];
				$f = (object)$f;
				if (isset($f->object) && ($f->object === $this)) {
					$stack[] = $f;
					if ($this->is_test($f->function)) {
						break;
					}
				}
			}
			$obj = new \stdClass();
			$obj->stack = $stack;
			return $obj;
		}
	}
	/**
	 * Part of public interface. Called to unconditionally assert fgailure.
	 *
	 */
	public function fail()
	{
		$frame = $this->assert_called_from();
		$this->record_fail($frame);
	}

	public function assertNull($prove)
	{
		$frame = $this->assert_called_from();
		if ($prove === null) {
			$this->record_pass($frame);
			return $this->pass();
		}
		$this->record_fail($frame);
	}
	public function assertNotNull($prove)
	{
		$frame = $this->assert_called_from();
		if ($prove !== null) {
			$this->record_pass($frame);
			return $this->pass();
		}
		$this->record_fail($frame);
	}
	public function assertTrue($prove)
	{
		$frame = $this->assert_called_from();
		if ($prove === true) {
			$this->record_pass($frame);
			return $this->pass();
		}
		$this->record_fail($frame);
	}
	public function assertFalse($prove)
	{
		$frame = $this->assert_called_from();
		if ($prove === false) {
			$this->record_pass($frame);
			return $this->pass();
		}
		$this->record_fail($frame);
	}
	public function assertEqual($expected, $prove)
	{
		$frame = $this->assert_called_from();
		if ((is_bool($prove) or is_bool($expected)) and ($expected !== $prove)) {
			$this->record_fail($frame);
			return $this->rec_fail($fail_message);
		}
			
		if ($expected == $prove) {
			$this->record_pass($frame);
			return $this->pass();
		}
		$this->record_fail($frame);
	}

	public function assertEquals($expected, $prove)
	{
		$frame = $this->assert_called_from();
		if ((is_bool($prove) or is_bool($expected)) and ($expected !== $prove)) {
			$this->record_fail($frame);
		}
			
		if ($expected == $prove) {
			$this->record_pass($frame);
			return $this->pass();
		}
		$this->record_fail($frame);
	}
	public function assertNotEqual($expected, $prove)
	{
		$frame = $this->assert_called_from();
		if ((is_bool($prove) or is_bool($expected)) and ($expected === $prove)) {
			$this->record_fail($frame);
		}
			
		if ($expected != $prove) {
			$this->record_pass($frame);
			return $this->pass();
		}
		$this->record_fail($frame);
	}
	public function assert_true($prove)
	{
		$frame = $this->assert_called_from();
		if ($prove === true) {
			$this->record_pass($frame);
			return $this->pass();
		}
		$this->record_fail($frame);
	}
	
	public function assert_false($prove)
	{
		$frame = $this->assert_called_from();
		if ($prove === false) {
			$this->record_pass($frame);
			return $this->pass();
		}
		$this->record_fail($frame);
	}
	
	public function assert_equals($expected, $prove)
	{
		$frame = $this->assert_called_from();
		if ((is_bool($prove) or is_bool($expected)) and ($expected !== $prove)) {
			$this->record_fail($frame);
		}
			
		if ($expected == $prove) {
			$this->record_pass($frame);
			return $this->pass();
		}
		$this->record_fail($frame);
	}
	/**
	 * Uses Reflection to get the names of all the test methods in this testcase.
	 * @return array
	 */
	public function get_tests() : array
	{
		$reflected_self = new \ReflectionClass($this);
		
		$tests = [];
		foreach ($reflected_self->getMethods() as $one_method) {
			$my_klass_name = get_class($this);
			$declaring_class_name = $one_method->getDeclaringClass()->getName();
			$method_name = $one_method->name;
			if ($this->is_test($method_name)) $tests[] = $method_name;
		}
		return $tests;
	}
	/**
	 * tests is a method name conforms to the naming convention
	 * for a test method.
	 * @param string $method_name Name of a candidate method.
	 * @return boolean
	 */
	protected function is_test($method_name) : bool
	{
		return (preg_match(self::TEST_METHOD_REGEX, $method_name) == 1);
	}
	/**
	 * Run all the tests methods in $this testcase
	 * @return array Of TestResult
	 */
	public function run() : array
	{
		$results = [];
		foreach ($this->get_tests() as $one_test) {
			$results[$one_test] = $this->run_one($one_test);
		}
		return $results;
	}
	/**
	 * Run a named test in this testcase.
	 * @param string $test_name The name of the test method.
	 * @return TestResult
	 */
	public function run_one(string $test_name) : TestResult
	{
		$this->active_result = new TestResult($this, $test_name);

		$this->active_test = $test_name;
		try {
			$this->active_test = $test_name;
			$start_time = microtime(true);
			$this->before_each();
			$this->setUp();
			$this->$test_name();
		} catch (\Exception $exception) {
			$this->store_exception($exception);
		}

		$this->after_each();
		$this->active_test = null;
		$this->tearDown();
		$result_time = microtime(true) - $start_time;
		$this->active_result->set_running_time($result_time);
		return $this->active_result;
	}
	
	public function before_each()
	{
	}
	public function setUp()
	{
	}

	public function after_each()
	{
	}
	public function tearDown()
	{
	}
}
