<?php
require_once dirname(__DIR__ ). '/vendor/autoload.php';

class TestCaseMyClass extends LiteTest\TestCase
{

	function before_each()
	{
		// print_r(\Bootstrap::config());
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


class MyClass
{

}