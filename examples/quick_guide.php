<?php
require_once dirname(__DIR__). '/vendor/autoload.php';

class TestCaseMyClass extends LiteTest\TestCase
{

	function before_each()
	{
	}
	function after_each()
	{
	}
	function test_1_my_class()
	{
		$my_class = new MyClass();
		$this->assert_true($my_class instanceof MyClass);
	}
	function test_2_my_class()
	{
		$my_class = new MyClass();
		$this->assert_true(false);
		$this->assert_true($my_class instanceof MyClass);
	}
	function test_3_my_class()
	{
		$my_class = new MyClass();
		$this->assert_true($my_class instanceof MyClass);
	}
	function test_4_my_class()
	{
		$my_class = new MyClass();
		$this->assert_true($my_class instanceof MyClass);
	}
}


// 3. Choose a TestRunner and add your TestCase (CLI runner in this case)
$runner = new LiteTest\TestRunnerCLI();
$runner->add_test_case(new TestCaseMyClass());


// 4. Run your tests
$runner->print_results();


class MyClass
{

}
