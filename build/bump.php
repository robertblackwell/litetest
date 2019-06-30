#!/usr/bin/env php
<?php

$version_file  = dirname(__DIR__)."/version.json";

$v = json_decode(file_get_contents($version_file));

$v->patch++;

file_put_contents($version_file, json_encode($v, JSON_PRETTY_PRINT));

$major = $v->major .".";
$minor = $v->minor .".";
$patch = $v->patch;

echo "v".$major . $minor . $patch ."\n";
$version =  $major . $minor . $patch ;

$V =<<<EOD
<?php
namespace LiteTest;
class Version
{
	public static \$version = "$version";
}

EOD;

file_put_contents(dirname(__DIR__)."/src/LiteTest/Version.php", $V);
