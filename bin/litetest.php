<?php
// print __METHOD__." dirname 1 of file  ". dirname(__FILE__) ."\n";
// print __METHOD__." dirname 2 of file  ". dirname(dirname(__FILE__)) ."\n";
// print __METHOD__." dirname 3 of file  ". dirname(dirname(dirname(__FILE__))) ."\n";
// print __METHOD__." dirname 4 of file  ". dirname(dirname(dirname(dirname(__FILE__)))) ."\n";

/**
* See if we are running as a project installed package
*/
$vendor_dir = dirname(dirname(dirname(dirname(__FILE__))));
$info = new SplFileInfo($vendor_dir);
if ($info->getBasename() === "vendor") {
	require "{$vendor_dir}/autoload.php";
} else {
	/**
	* Now test to see if we are a global package - THERE IS A PROBLEM WITH THIS
	*/
	$vendor_dir = dirname(dirname(__FILE__));
	$info = new SplFileInfo($vendor_dir);
	if ($info->getBasename() !== "vendor") {
		throw new \Exception("require vendor autoload is asking for wrong file {$vendor_dir}");
	}
	require "{$vendor_dir}/autoload.php";
}
include dirname(dirname(__FILE__))."/src/main.php";
