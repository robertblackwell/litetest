<?php

require dirname(__DIR__)."/vendor/autoload.php";

require_once dirname(__FILE__)."/..".DIRECTORY_SEPARATOR."LiteTestPHP.php";

$test_runner = new LiteTest\TestRunnerCLI();

require_once dirname(__FILE__)."/battery.php";

$test_runner->print_results();
