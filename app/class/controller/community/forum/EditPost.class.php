<?php
final class EditPost extends Controller {
	
	private $post = null;
	private $thread = null;
	private $forum = null;
	
	private $message = "";
	
	private $messageError = -1;
	
	private $preview = false;
	private $genericError = false;
	
	public function init() {
		$this->requireLogin = true;
		if(!$this->isLoggedIn()) {
			return;
		}
		
		$id = $_GET['id'] ?? null;
		if($id === null) {
			return;
		}
		
		if(!is_numeric($id)) {
			return;
		}
		
		$id = (int) $id;
		if($id < 1) {
			return;
		}
		
		$this->setPost($id);
		if($this->post === null) {
			return;
		}
		
		$this->setThread($this->post->threadId);
		if($this->thread === null) {
			return;
		}
		
		$this->setForum($this->thread->forumId);
		if($this->forum === null) {
			return;
		}
		
		if(isset($_POST['previewButton'])) {
			$this->messageError = $this->validateMessage();
			
			if($this->messageError === -1) {
				$this->preview = true;
			}
		} else if(isset($_POST['submitButton'])) {
			$this->messageError = $this->validateMessage();
				
			if($this->messageError === -1) {
				if(!$this->submitEdit()) {
					$this->genericError = true;
				}
			}
		} else if(isset($_POST['cancelButton'])) {
			header('Location: '. url('forum/viewthread', EXT, '?id='. $this->thread->id));
			die('Please wait...');
		}
	}
	
	private function submitEdit() : bool {
		$c = $this->dbh->con;
		
		$date = DateUtil::currentSqlDate();
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$stmt = $this->dbh->con->prepare('
			UPDATE `forum_posts` 
			SET `message` = :msg, 
				`lastEditDate` = :date,
				`lastEditor` = :uname, 
				`lastEditorId` = :uid,
				`lastEditorIP` = :uip 
			WHERE `id` = :pid 
				AND `hidden` = 0 
			LIMIT 1;
		');
		$stmt->bindParam(':msg', $this->message, PDO::PARAM_STR);
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':uname', $this->user->username, PDO::PARAM_STR);
		$stmt->bindParam(':uid', $this->user->id, PDO::PARAM_INT);
		$stmt->bindParam(':uip', $ip, PDO::PARAM_STR);
		$stmt->bindParam(':pid', $this->post->id, PDO::PARAM_INT);
		if(!$stmt->execute()) {
			return false;
		}
		
		header('Location: '. url('forum/viewthread', EXT, '?id='. $this->thread->id .'&post='. $this->post->id .'#post'. $this->post->id));
		return true;
	}
	
	private function validateMessage() : int {
		$message = $_POST['messageInput'] ?? null;
		if($message === null || !is_string($message)) {
			return 0;
		}
		
		$message = trim($message);
		if($message === "") {
			return 0;
		}
		
		if($this->user->staff) {
			if(strlen($message) > 60000) {
				return 1;
			}
		} else {
			if(strlen($message) > 5000) {
				return 1;
			}
		}
		
		$this->message = $message;
		return -1;
	}
	
	private function setPost(int $id) {
		$stmt = $this->dbh->con->prepare('
			SELECT `p`.`id`, `p`.`authorId`, `p`.`date`, `p`.`message`, `p`.`threadId`, 
				`a`.`username` AS `author`, `a`.`staff` AS `authorStaff`, `a`.`mod` AS `authorMod` 
			FROM `forum_posts` AS `p` 
				JOIN `user_accounts` AS `a` 
					ON `p`.`authorId` = `a`.`id` 
			WHERE `p`.`id` = :pid 
				AND `p`.`hidden` = 0 
			LIMIT 1;
		');
		$stmt->bindParam(':pid', $id, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_OBJ);
		
		if(!$result) {
			return;
		}
		
		$this->post = $result;
		$this->message = $this->post->message;
	}
	
	private function setThread(int $id) {
		$stmt = $this->dbh->con->prepare('
			SELECT `id`, `title`, `forumId`, `locked`, `posts`  
			FROM `forum_threads` 
			WHERE `id` = :id 
				AND `hidden` = 0 
			LIMIT 1;
		');
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_OBJ);
		
		if(!$result) {
			return;
		}
		
		$this->thread = $result;
	}
	
	private function setForum(int $id) {
		$stmt = $this->dbh->con->prepare('
			SELECT `id`, `name` 
			FROM `forum_forums`
			WHERE `id` = :id
			LIMIT 1;
		');
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_OBJ);
		
		if(!$result) {
			return;
		}
		
		$this->forum = $result;
	}
	
	public function getPost() {
		return $this->post;
	}
	
	public function getThread() {
		return $this->thread;
	}

	public function getForum() {
		return $this->forum;
	}
	
	public function getMessage() : string {
		return $this->message;
	}
	
	public function getMessageError() : int {
		return $this->messageError;
	}
	
	public function getGenericError() : bool {
		return $this->genericError;
	}
	
	public function isPreviewing() : bool {
		return $this->preview;
	}
	
}
?>