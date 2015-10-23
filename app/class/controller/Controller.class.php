<?php
abstract class Controller {
	
	protected $dbh = null;
	
	public final function __construct(string $dbHost, string $dbName, string $dbUser, string $dbPass) {
		$this->dbh = new DBConnection($dbHost, $dbName, $dbUser, $dbPass);
		$this->init();
	}
	
	public abstract function init();
	
	public function requireLogin() : bool {
		return false;
	}
	
}
?>