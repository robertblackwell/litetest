<?php
$debug = false;
$vendor_dir = dirname(dirname(__FILE__))."/vendor";

if (! is_dir($vendor_dir)) {
	// then running from installed package
	if ($debug)
		print "running from package \n";
	$vendor_dir = dirname(dirname(dirname(dirname(__FILE__))));
}
if ($debug) {
	print "vendor_dir: {$vendor_dir} \n";
	print __METHOD__." dirname 2 of file  ". dirname(dirname(__FILE__)) ."\n";
	print __METHOD__." dirname 3 of file  ". dirname(dirname(dirname(__FILE__))) ."\n";
	print __METHOD__." dirname 4 of file  ". dirname(dirname(dirname(dirname(__FILE__)))) ."\n";
}
$info = new SplFileInfo($vendor_dir);

if ($info->getBasename() !== "vendor") {
	throw new \Exception("require vendor autoload is asking for wrong file {$vendor_dir}");
}
require "{$vendor_dir}/autoload.php";
include dirname(dirname(__FILE__))."/src/Main.php";
