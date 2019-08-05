<?php
use Symfony\Component\Console\Application;

use LiteTest\Commands\InitCommand;
use LiteTest\Commands\RunCommand;
use LiteTest\Version;

$application = new Application("Release", Version::getVersion());
$application->add(new InitCommand());
$application->add(new RunCommand());
$application->run();
