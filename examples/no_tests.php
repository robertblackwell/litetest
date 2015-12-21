<?php
require_once dirname(__DIR__ ). '/vendor/autoload.php';

class TestCaseMyClass2 extends LiteTest\TestCase
{

	function before_each()
	{
	}
	function after_each()
	{
	}
	function aatest_1_my_class()
	{
		$my_class = new MyClass2();
		$this->assert_true($my_class instanceof MyClass2);
	}
	function aatest_2_my_class()
	{
		$my_class = new MyClass2();
		$this->assert_true(false);
		$this->assert_true($my_class instanceof MyClass2);
	}
	function aatest_3_my_class()
	{
		$my_class = new MyClass2();
		$this->assert_true($my_class instanceof MyClass2);
	}
	function aatest_4_my_class()
	{
		$my_class = new MyClass2();
		$this->assert_true($my_class instanceof MyClass2);
	}
}


class MyClass2
{

}