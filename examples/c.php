<?php
require_once dirname(__DIR__). '/vendor/autoload.php';


class Kolor extends Colors\Color
{
	function __invoke($string = null)
	{
		$obj = new Colors\Color($string);
		return $obj;
	}
}

$c = new Kolor();
$c = new Colors\Color;

function color($string)
{
	return new Colors\Color($string);
}

echo
	color('Hello World!')->red()->white()
	. color("some other text 1 ")->green()
	. color("some other text 2 ")->blue()
	. color("some other text 3 ")->yellow()
	. color("some other text 4 ")->white()
	. PHP_EOL;

exit();
print "we r here \n";

print $c->red("this is red")."\n";

print $c->bold($c->red("this is red and bold"))."\n";

print $c("also this is red and bold")->red->bold."\n";

$c("this is some text");
var_dump($c);
print "===============================================================\n";
$c("this is some text")->red();
var_dump($c);
print "===============================================================\n";
$c("different text");
var_dump($c);
print "===============================================================\n";
$c("different text")->blue();
var_dump($c);
print "===============================================================\n";
