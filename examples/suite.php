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
	function tEst_2_my_class()
	{
		$my_class = new MyClass();
		$this->assert_true(false);
		$this->assert_true($my_class instanceof MyClass);
	}
	function my_3_my_class_test()
	{
		$my_class = new MyClass();
		$this->assert_true($my_class instanceof MyClass);
	}
	function my_class_tEst()
	{
		$my_class = new MyClass();
		$this->assert_true($my_class instanceof MyClass);
	}
}
class MyClassTest extends LiteTest\TestCase
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
	function tEst_2_my_class()
	{
		$my_class = new MyClass();
		$this->assert_true(false);
		$this->assert_true($my_class instanceof MyClass);
	}
	function my_3_my_class_test()
	{
		$my_class = new MyClass();
		$this->assert_true($my_class instanceof MyClass);
	}
	function my_class_tEst()
	{
		print __METHOD__."\n";
		$my_class = new MyClass();
		$this->assert_true($my_class instanceof MyClass);
	}
}


class MyClass
{

}