<?php
require_once dirname(__DIR__ ). '/vendor/autoload.php';

print "BOOTSTRAP XXX being loaded \n";
class Bootstrap{

	static function config()
	{
		$config = ["key" => "value"];
		return $config;
	}
}
