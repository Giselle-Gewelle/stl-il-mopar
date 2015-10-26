<?php
abstract class Controller {
	
	protected $storeLocation = true;
	
	protected $user = null;
	protected $dbh = null;
	
	public final function __construct(string $dbHost, string $dbName, string $dbUser, string $dbPass) {
		$this->dbh = new DBConnection($dbHost, $dbName, $dbUser, $dbPass);
		$this->checkLogin();
		$this->init();
		
		if($this->storeLocation) {
			$this->setLocation();
		}
	}
	
	private final function setLocation() {
		$url = 'http';
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
			$url = 'https';
		}
		
		$url .= '://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		
		$parsedUrl = parse_url($url);
		
		$path = $parsedUrl['path'] ?? '';
		
		if(strpos($path, '/') === 0) {
			$path = substr($path, 1);
		}
		if(strpos($path, 'stlmodernmopar/') === 0) {
			$path = substr($path, strlen('stlmodernmopar/'));
		}
		
		$query = $parsedUrl['query'] ?? '';
		if($query !== '') {
			$path .= '?'. $query;
		}
		
		setcookie('lastLocation', $path, time() + 60 * 60 * 24 * 7, '/');
	}
	
	private final function checkLogin() {
		$hash = $_COOKIE['moparSessId'] ?? null;
		if($hash === null || !is_string($hash) || strlen($hash) !== 128) {
			return;
		}
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$stmt = $this->dbh->con->prepare('
			SELECT `u`.`id`, `u`.`username`, `u`.`mod`, `u`.`staff`, 
				`s`.`id` AS `sessionId` 
			FROM `user_accounts` AS `u` 
				JOIN `user_sessions` AS `s` 
					ON `u`.`id` = `s`.`userId` 
			WHERE `s`.`hash` = :hash 
				AND `s`.`userIP` = :ip 
				AND `s`.`active` = 1 
			LIMIT 1;
		');
		$stmt->bindParam(':hash', $hash, PDO::PARAM_STR);
		$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
		if(!$stmt->execute()) {
			return;
		}
		
		$result = $stmt->fetch(PDO::FETCH_OBJ);
		if(!$result) {
			return;
		}
		
		$this->user = $result;
		
		$date = DateUtil::currentSqlDate();
		
		$stmt = $this->dbh->con->prepare('
			UPDATE `user_accounts` 
			SET `lastActive` = :date, 
				`lastIP` = :ip 
			WHERE `id` = :id 
			LIMIT 1;
		');
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
		$stmt->bindParam(':id',	$this->user->id, PDO::PARAM_INT);
		$stmt->execute();
		
		$newHash = Hashing::generateSessionHash($ip);
		
		$stmt = $this->dbh->con->prepare('
			UPDATE `user_sessions` 
			SET `hash` = :hash 
			WHERE `id` = :id 
			LIMIT 1;
		');
		$stmt->bindParam(':hash', $newHash, PDO::PARAM_STR);
		$stmt->bindParam(':id', $this->user->sessionId, PDO::PARAM_INT);
		$stmt->execute();
		
		setcookie('moparSessId', $newHash, time() + 60 * 60 * 24 * 7, '/');
	}
	
	public abstract function init();
	
	public function requireLogin() : bool {
		return false;
	}
	
	public final function getUser() {
		return $this->user;
	}
	
	public final function isLoggedIn() : bool {
		return $this->user !== null;
	}
	
}
?>