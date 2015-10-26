<?php
require_once('config.php');

function importClass($classPath) {
	require_once(APP_DIR . DIRECTORY_SEPARATOR .'class'. DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $classPath) .'.class.php');
}

function cleanUp() {
	unset($className);
	unset($ctrl);
}

function url($url, $ext = EXT, $params = '') : string {
	return URL .'/'. $url . $ext . $params;
}

function safe($text) {
	return htmlspecialchars($text);
}

importClass('db.DBConnection');
importClass('security.Hashing');
importClass('util.DateUtil');
importClass('controller.Controller');
importClass($controllerClass);

$className = substr($controllerClass, strrpos($controllerClass, '.') + 1);
$ctrl = new $className('127.0.0.1', 'stlmodernmopar', 'root', 'mysql');
?>