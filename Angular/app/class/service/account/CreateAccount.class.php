<?php
final class CreateAccount extends Service {
	
	public function init() {
		$this->response = (new class {
			public $cityStateError = false;
			public $usernameError = -1;
			public $passwordError = -1;
			public $termsError = false;
			public $successful = true;
		});
		
		$validateState = $this->validateState();
		$validateCity = $this->validateCity();
		$validateUsername = $this->validateUsername();
		$validatePasswords = $this->validatePasswords();
		$validateTerms = $this->validateTerms();
		
		if(!$validateState || !$validateCity) {
			$this->response->cityStateError = true;
			$this->response->successful = false;
		}
		if($validateUsername !== -1) {
			$this->response->usernameError = $validateUsername;
			$this->response->successful = false;
		}
		if($validatePasswords !== -1) {
			$this->response->passwordError = $validatePasswords;
			$this->response->successful = false;
		}
		if(!$validateTerms) {
			$this->response->termsError = true;
			$this->response->successful = false;
		}
		
		if($this->response->successful) {
			$username = trim($this->formData->username);
			$password = trim($this->formData->password1);
			$salt = Hashing::generateSalt();
			$password = Hashing::hashPassword($password, $salt);
			$state = trim($this->formData->state);
			$city = trim($this->formData->city);
			$ip = $_SERVER['REMOTE_ADDR'];
			$date = DateUtil::currentSqlDate();
			
			$stmt = $this->dbh->con->prepare('
				INSERT INTO `user_accounts` (
					`username`, `password`, `salt`, `state`, `city`, `creationDate`, `creationIP`, `lastIP`
				) VALUES (
					:uname, :pass, :salt, :state, :city, :date, :ip1, :ip2
				);
			');
			$stmt->bindParam(':uname', $username, PDO::PARAM_STR);
			$stmt->bindParam(':pass', $password, PDO::PARAM_STR);
			$stmt->bindParam(':salt', $salt, PDO::PARAM_STR);
			$stmt->bindParam(':state', $state, PDO::PARAM_STR);
			$stmt->bindParam(':city', $city, PDO::PARAM_STR);
			$stmt->bindParam(':date', $date, PDO::PARAM_STR);
			$stmt->bindParam(':ip1', $ip, PDO::PARAM_STR);
			$stmt->bindParam(':ip2', $ip, PDO::PARAM_STR);
			$stmt->execute();
		}
	}
	
	private function validateTerms() : bool {
		$terms = $this->formData->terms ?? null;
		if($terms === null || !is_bool($terms)) {
			return false;
		}
		
		return $terms;
	}
	
	private function validatePasswords() : int {
		$pass1 = $this->formData->password1 ?? null;
		$pass2 = $this->formData->password2 ?? null;
		
		if($pass1 === null || !is_string($pass1)) {
			return 0;
		}
		
		$pass1 = trim($pass1);
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
		
		return -1;
	}
	
	private function validateUsername() : int {
		$username = $this->formData->username ?? null;
		if($username === null || !is_string($username)) {
			return 0;
		}
		
		// Strip out extra spaces
		$username = trim($username);
		while(strpos($username, '  ') !== false) {
			$username = str_replace('  ', ' ', $username);
		}
		
		if($username === "") {
			return 0;
		}
		
		if(strlen($username) > 15) {
			return 1;
		}
		
		if(!preg_match('/^[A-Za-z0-9 ]{1,15}$/', $username)) {
			return 2;
		}
		
		$count = -1;
		
		$stmt = $this->dbh->con->prepare('
			SELECT COUNT(`id`) AS `count` 
			FROM `user_accounts` 
			WHERE LOWER(`username`) = LOWER(:username) 
			LIMIT 1;
		');
		$stmt->bindParam(':username', $username, PDO::PARAM_STR);
		$stmt->execute();
		$return = $stmt->fetch();
		
		if($return['count'] == -1) {
			return 4;
		} else if($return['count'] == 1) {
			return 3;
		}
		
		return -1;
	}
	
	private function validateCity() : bool {
		$city = $this->formData->city ?? null;
		if($city === null || !is_string($city)) {
			return false;
		}
		
		if(!preg_match('/^[A-Za-z \'-]{3,50}$/', $city)) {
			return false;
		}
		
		return true;
	}
	
	private function validateState() : bool {
		$state = $this->formData->state ?? null;
		if($state === null || !is_string($state)) {
			return false;
		}
		
		if($state !== "IL" && $state !== "MO" && $state !== "O") {
			return false;
		}
		
		return true;
	}
	
}
?>