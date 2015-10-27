<?php
final class ChangePassword extends Controller {
	
	private $successful = false;
	private $error = -1;
	
	private $currentInput;
	private $newInput;
	
	public function init() {
		$this->requireLogin = true;
		if(!$this->isLoggedIn()) {
			return;
		}
		
		if(!isset($_POST['passwordChangeSubmit'])) {
			return;
		}
		
		if(!$this->validateCurrentPassword()) {
			$this->error = 0;
			return;
		}
		
		$this->error = $this->validateNewPasswords();
		if($this->error !== -1) {
			return;
		}
		
		$stmt = $this->dbh->con->prepare('
			SELECT `password`, `salt`
			FROM `user_accounts`
			WHERE `id` = :id
			LIMIT 1;
		');
		$stmt->bindParam(':id', $this->user->id, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_OBJ);
		if(!$result) {
			$this->error = 6;
			return;
		}
		
		$inputHash = Hashing::hashPassword($this->currentInput, $result->salt);
		if($inputHash !== $result->password) {
			$this->error = 4;
			return;
		}
		
		if($this->currentInput === $this->newInput) {
			$this->error = 5;
			return;
		}
		
		$newSalt = Hashing::generateSalt();
		$newHash = Hashing::hashPassword($this->newInput, $newSalt);
		
		$stmt = $this->dbh->con->prepare('
			UPDATE `user_accounts` 
			SET `password` = :newHash, 
				`salt` = :newSalt 
			WHERE `id` = :id 
			LIMIT 1;
		');
		$stmt->bindParam(':newHash', $newHash, PDO::PARAM_STR);
		$stmt->bindParam(':newSalt', $newSalt, PDO::PARAM_STR);
		$stmt->bindParam(':id', $this->user->id, PDO::PARAM_INT);
		if(!$stmt->execute()) {
			$this->error = 6;
			return;
		}
		
		$date = DateUtil::currentSqlDate();
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$stmt = $this->dbh->con->prepare('
			INSERT INTO `user_passwordChanges` (
				`userId`, `date`, `ip`, `oldHash`, `oldSalt`, `newHash`, `newSalt` 
			) VALUES (
				:uid, :date, :ip, :oldHash, :oldSalt, :newHash, :newSalt
			);
		');
		$stmt->bindParam(':uid', $this->user->id, PDO::PARAM_INT);
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
		$stmt->bindParam(':oldHash', $result->password, PDO::PARAM_STR);
		$stmt->bindParam(':oldSalt', $result->salt, PDO::PARAM_STR);
		$stmt->bindParam(':newHash', $newHash, PDO::PARAM_STR);
		$stmt->bindParam(':newSalt', $newSalt, PDO::PARAM_STR);
		$stmt->execute();
		
		$this->successful = true;
	}
	
	private function validateCurrentPassword() : bool {
		$pass = $_POST['currentPassword'] ?? null;
		
		if($pass === null || !is_string($pass)) {
			return false;
		}
		
		$pass1 = trim($pass);
		if($pass === '') {
			return false;
		}
		
		if(strlen($pass) < 5 || strlen($pass) > 35) {
			return false;
		}
		
		if(!preg_match('/^[A-Za-z0-9]{5,35}$/', $pass)) {
			return false;
		}
		
		$this->currentInput = $pass;
		return true;
	}
	
	private function validateNewPasswords() : int {
		$pass1 = $_POST['password1'] ?? null;
		$pass2 = $_POST['password2'] ?? null;
		
		if($pass1 === null || !is_string($pass1)) {
			return 0;
		}
		
		$pass1 = trim($pass1);
		if($pass1 === '') {
			return 0;
		}
		
		if(strlen($pass1) < 5 || strlen($pass1) > 35) {
			return 1;
		}
		
		if(!preg_match('/^[A-Za-z0-9]{5,35}$/', $pass1)) {
			return 2;
		}
		
		if($pass2 === null || !is_string($pass2)) {
			return 3;
		}
		
		$pass2 = trim($pass2);
		if($pass1 !== $pass2) {
			return 3;
		}
		
		$this->newInput = $pass1;
		return -1;
	}
	
	public function isSuccessful() : bool {
		return $this->successful;
	}
	
	public function getErrorCode() : int {
		return $this->error;
	}
	
}
?>