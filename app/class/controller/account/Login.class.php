<?php
final class Login extends Controller {
	
	private $inputUsername = '';
	private $inputPassword = '';
	
	private $error = -1;
	
	public function init() {
		$this->storeLocation = false;
		
		if($this->isLoggedIn()) {
			$dest = $_COOKIE['lastLocation'] ?? '';
			header('Location: '. URL .'/'. $dest);
			die('Redirecting, please wait...');
		}
		
		if(isset($_POST['loginSubmit'])) {
			if(!$this->validateUsername() || !$this->validatePassword()) {
				$this->error = 0;
			}
			
			$this->validateLogin();
		}
	}
	
	private function validateLogin() {
		$stmt = $this->dbh->con->prepare('
			SELECT `id`, `password`, `salt` 
			FROM `user_accounts` 
			WHERE LOWER(`username`) = LOWER(:uname) 
			LIMIT 1;
		');
		$stmt->bindParam(':uname', $this->inputUsername, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_OBJ);
		if(!$result) {
			$this->error = 1;
			return;
		}
		
		$inputPasswordHash = Hashing::hashPassword($this->inputPassword, $result->salt);
		if($inputPasswordHash !== $result->password) {
			$this->error = 1;
			return;
		}
		
		$sessionHash = Hashing::generateSessionHash($_SERVER['REMOTE_ADDR']);
		$date = DateUtil::currentSqlDate();
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$stmt = $this->dbh->con->prepare('
			INSERT INTO `user_sessions` (
				`userId`, `userIP`, `hash`, `startDate` 
			) VALUES (
				:uid, :uip, :hash, :date
			);
		');
		$stmt->bindParam(':uid', $result->id, PDO::PARAM_INT);
		$stmt->bindParam(':uip', $ip, PDO::PARAM_STR);
		$stmt->bindParam(':hash', $sessionHash, PDO::PARAM_STR);
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		
		if(!$stmt->execute()) {
			$this->error = 2;
		} else {
			setcookie('moparSessId', $sessionHash, time() + 60 * 60 * 24 * 7, '/');
			$dest = $_COOKIE['lastLocation'] ?? '';
			header('Location: '. URL .'/'. $dest);
			die('Redirecting, please wait...');
		}
	}
	
	private function validatePassword() : bool {
		$pass = $_POST['loginPassword'] ?? null;
		
		if($pass === null || !is_string($pass)) {
			return false;
		}
		
		if(strlen($pass) < 5 || strlen($pass) > 35) {
			return false;
		}
		
		if(!preg_match('/^[A-Za-z0-9]{5,35}$/', $pass)) {
			return false;
		}
		
		$this->inputPassword = $pass;
		return true;
	}
	
	private function validateUsername() : bool {
		$username = $_POST['loginUsername'] ?? null;
		if($username === null || !is_string($username)) {
			return false;
		}
		
		// Strip out extra spaces
		$username = trim($username);
		while(strpos($username, '  ') !== false) {
			$username = str_replace('  ', ' ', $username);
		}
		
		if($username === "") {
			return false;
		}
		
		if(strlen($username) > 15) {
			return false;
		}
		
		if(!preg_match('/^[A-Za-z0-9 ]{1,15}$/', $username)) {
			return false;
		}
		
		$this->inputUsername = $username;
		return true;
	}
	
	public function getErrorCode() : int {
		return $this->error;
	}
	
}
?>