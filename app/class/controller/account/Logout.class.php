<?php
final class Logout extends Controller {
	
	public function init() {
		$this->storeLocation = false;
		
		if($this->isLoggedIn()) {
			$stmt = $this->dbh->con->prepare('
				UPDATE `user_sessions` 
				SET `active` = 0 
				WHERE `id` = :id 
				LIMIT 1;
			');
			$stmt->bindParam(':id', $this->user->sessionId, PDO::PARAM_INT);
			$stmt->execute();
			setcookie('moparSessId', '', 0, '/');
		}
		
		$dest = $_COOKIE['lastLocation'] ?? '';
		header('Location: '. URL .'/'. $dest);
		die('Redirecting, please wait...');
	}
	
}
?>