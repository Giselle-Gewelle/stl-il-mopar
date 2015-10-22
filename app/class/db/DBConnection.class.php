<?php

final class DBConnection {
	
	public $con;
	
	public function __construct(string $host, string $db, string $user, string $pass) {
		try {
			$this->con = new PDO('mysql:host='. $host .';dbname='. $db, $user, $pass);
		} catch(PDOException $e) {
			die();
		}
	}
	
}

?>