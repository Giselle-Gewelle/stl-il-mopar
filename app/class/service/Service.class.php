<?php
abstract class Service {
	
	protected $response = null;
	protected $formData = null;
	protected $dbh = null;
	
	public function __construct(string $dbHost, string $dbName, string $dbUser, string $dbPass) {
		$this->dbh = new DBConnection($dbHost, $dbName, $dbUser, $dbPass);
		
		$this->formData = file_get_contents('php://input');
		$this->formData = json_decode($this->formData);
	}
	
	public abstract function init();
	
	public final function getResponse() {
		$this->init();
		
		return json_encode($this->response);
	}
	
	public function requireLogin() {
		return false;
	}
	
}
?>