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
	function test_1_my_class()
	{
		$my_class = new MyClass2();
		$this->assert_true($my_class instanceof MyClass2);
		$this->assert_equals("one", "two");
	}
	function test_2_my_class()
	{
		$my_class = new MyClass2();
		$this->assert_true(false);
		$this->assert_true($my_class instanceof MyClass2);
	}
	function test_3_my_class()
	{
		$my_class = new MyClass2();
		$this->assert_true($my_class instanceof MyClass2);
	}
	function test_4_my_class()
	{
		$my_class = new MyClass2();
		$this->assert_true($my_class instanceof MyClass2);
		$this->assertEqual(1,32);
		$this->assert_equals("one", "two");
	}
}


class MyClass2
{

}