<?php

final class DBConnection {
	
	public $con;
	
	public function __construct(string $host, string $db, string $user, string $pass) {
		try {
			$this->con = new PDO('mysql:host='. $host .';dbname='. $db, $user, $pass);
			$this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			die();
		}
	}
	
	public function rollback() {
		if($this->con->inTransaction()) {
			$this->con->rollBack();
		}
	}
	
}

?>