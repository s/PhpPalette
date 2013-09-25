<?php

define('ROOT_DIR', realpath(dirname(__FILE__)));

require_once ROOT_DIR.'/classes/Exception/PException.php';

require_once ROOT_DIR.'/classes/Palette.php';

date_default_timezone_set('Europe/Istanbul');

ini_set("log_errors", 1);

ini_set("error_log", ROOT_DIR."/outputs/logs/app.log");

date_default_timezone_set('Europe/Istanbul');

error_reporting(E_ALL ^ E_NOTICE);

$image_path = $argv[1];

if (isset($image_path)) {
	
	$palette = new Palette($image_path,1);

	$palette->process();

}else{
	throw new PException('InvalidInputException.',"Image file or image type is not specified.",1);
}