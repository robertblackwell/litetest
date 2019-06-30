#!/usr/bin/env php
<?php
require_once(dirname(__FILE__)."/PHPFiles.php");
/**
** Generalized somewhat to have arguments - the goal is not to have it depend on its environment
* @return void
*/
function print_usage()
{
	print "
		create-phar.php  target srcPrefix stub phpfiles ......

			target 		-	is the full path of the phar archive to be msql_created
			srcPrefix 	-	is the path leading to but not including the top level source dir
			stub 		-	is the full path of the phar stub file
			phpfiles 	-	a space separated list of php file names. The name is a path relative to 
							srcDir

	";
}

if (count($argv) <= 2) {
	die("too few arguments");
	print_usage();
}
$projectRoot = dirname(dirname(__FILE__));
print $projectRoot."\n";
$srcRoot = dirname(__FILE__)."/src";

$target = $argv[1];
$phar_file = basename($target);
$srcPrefix = realpath($argv[2]);
$stub = $argv[3];
$archive_name = "archive";

$finder = new \Mkphar\PHPFiles();

$php_files = array();

for ($i = 4; $i < count($argv); $i++) {
	print "adding path ".$argv[$i]." to the collection\n";
	$finder->addPath($argv[$i]);
}
$php_files = $finder->getFiles();

print "Building $target with phar name of $phar_file from source code in $srcPrefix \n";
//print "php files ".implode("\n", $php_files)."\n";

$phar = new Phar(
	$target, 
    FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, 
    basename($target)
    );

foreach($php_files as $file){

	print "adding $file to archive ". basename($target) ." from ".$srcPrefix. "/". $file."\n";
	if( $file == $stub ) continue;
	$phar[$file] = file_get_contents($srcPrefix ."/". $file);
}

// $phar["Synchronizer.php"] = file_get_contents($srcRoot . "/Synchronizer.php");
// $phar["Commands/ConfigObject.php"] = file_get_contents($srcRoot . "/ConfigObject.php");

// $list = glob($srcRoot.'/Commands/*.php');

// foreach($list as $file){
// 	$bn = basename($file);
// 	$phar["Commands/".$bn] = file_get_contents($srcRoot . "/Commands/".$bn);
// }

$phar->setStub(file_get_contents($srcPrefix."/".$stub));

print "phar build complete\n";
