<?php
final class CreateAccount extends Controller {
	
	private $successful = false;
	
	private $values = null;
	private $errors = null;
	private $password = '';
	
	public function init() {
		$this->values = (new class() {
			public $city = '';
			public $state = '';
			public $username = '';
			public $terms = false;
		});
		
		$this->errors = (new class() {
			public $cityState = false;
			public $username = -1;
			public $password = -1;
			public $terms = false;
		});
		
		if(isset($_POST['inputCreateSubmit'])) {
			$validateState = $this->validateState();
			$validateCity = $this->validateCity();
			$validateUsername = $this->validateUsername();
			$validatePasswords = $this->validatePasswords();
			$validateTerms = $this->validateTerms();
			
			$successful = true;
			
			if(!$validateState || !$validateCity) {
				$this->errors->cityState = true;
				$successful = false;
			}
			if($validateUsername !== -1) {
				$this->errors->username = $validateUsername;
				$successful = false;
			}
			if($validatePasswords !== -1) {
				$this->errors->password = $validatePasswords;
				$successful = false;
			}
			if(!$validateTerms) {
				$this->errors->terms = true;
				$successful = false;
			}
			
			if($successful) {
				$username = trim($this->values->username);
				$password = trim($this->password);
				$salt = Hashing::generateSalt();
				$password = Hashing::hashPassword($password, $salt);
				$state = trim($this->values->state);
				$city = trim($this->values->city);
				$ip = $_SERVER['REMOTE_ADDR'];
				$date = DateUtil::currentSqlDate();
					
				$stmt = $this->dbh->con->prepare('
					INSERT INTO `user_accounts` (
						`username`, `password`, `salt`, `state`, `city`, `creationDate`, `creationIP`, `lastActive`, `lastIP`
					) VALUES (
						:uname, :pass, :salt, :state, :city, :date1, :ip1, :date2, :ip2
					);
				');
				$stmt->bindParam(':uname', $username, PDO::PARAM_STR);
				$stmt->bindParam(':pass', $password, PDO::PARAM_STR);
				$stmt->bindParam(':salt', $salt, PDO::PARAM_STR);
				$stmt->bindParam(':state', $state, PDO::PARAM_STR);
				$stmt->bindParam(':city', $city, PDO::PARAM_STR);
				$stmt->bindParam(':date1', $date, PDO::PARAM_STR);
				$stmt->bindParam(':ip1', $ip, PDO::PARAM_STR);
				$stmt->bindParam(':date2', $date, PDO::PARAM_STR);
				$stmt->bindParam(':ip2', $ip, PDO::PARAM_STR);
				$stmt->execute();
				
				$this->successful = true;
			}
		}
	}
	
	public function isSuccessful() {
		return $this->successful;
	}
	
	public function getValues() {
		return $this->values;
	}
	
	public function getErrors() {
		return $this->errors;
	}
	
	private function validateTerms() : bool {
		$terms = $_POST['inputCreateTerms'] ?? null;
		if($terms === null || $terms !== 'on') {
			return false;
		}
		
		$this->values->terms = true;
		return $terms;
	}
	
	private function validatePasswords() : int {
		$pass1 = $_POST['inputCreatePassword1'] ?? null;
		$pass2 = $_POST['inputCreatePassword2'] ?? null;
		
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
		
		$this->password = $pass1;
		return -1;
	}
	
	private function validateUsername() : int {
		$username = $_POST['inputCreateUsername'] ?? null;
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
		
		$this->values->username = $username;
		return -1;
	}
	
	private function validateCity() : bool {
		$city = $_POST['inputCreateCity'] ?? null;
		if($city === null || !is_string($city)) {
			return false;
		}
		
		if(!preg_match('/^[A-Za-z \'-.,]{3,50}$/', $city)) {
			return false;
		}
		
		$this->values->city = $city;
		return true;
	}
	
	private function validateState() : bool {
		$state = $_POST['inputCreateState'] ?? null;
		if($state === null || !is_string($state)) {
			return false;
		}
		
		if($state !== 'IL' && $state !== 'MO' && $state !== 'O') {
			return false;
		}
		
		$this->values->state = $state;
		return true;
	}
	
}
?>