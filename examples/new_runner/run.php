<?php

require dirname(dirname(__DIR__))."/vendor/autoload.php";

// require_once dirname(__FILE__)."/..".DIRECTORY_SEPARATOR."LiteTestPHP.php";
require_once dirname(dirname(__FILE__))."/suite.php";

$test_runner = new LiteTest\CliRunner();

// require_once dirname(__FILE__)."/battery.php";
$test_runner->run();
