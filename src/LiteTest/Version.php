<?php
namespace LiteTest;
class Version
{
	public static $version = "2.0.0";
	public static function getVersion()
	{
		$vf = dirname(dirname(__FILE__))."/version.json";
		$vc = file_get_contents($vf);
		$vo = json_decode($vc);
		$v = $vo->versionString;
		return $v;
	}
}
