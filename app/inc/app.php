<?php
require_once('config.php');

function importClass($classPath) {
	require_once(APP_DIR . DIRECTORY_SEPARATOR .'class'. DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $classPath) .'.class.php');
}

if(PAGE_TYPE === 'content') {
	
} else if(PAGE_TYPE === 'service') {
	importClass('db.DBConnection');
	importClass('service.Service');
	importClass('security.Hashing');
	importClass('util.DateUtil');
	
	importClass($serviceClass);
	$className = substr($serviceClass, strrpos($serviceClass, '.') + 1);
	$svc = new $className('127.0.0.1', 'stlmodernmopar', 'root', 'mysql');
	echo $svc->getResponse();
}
?>