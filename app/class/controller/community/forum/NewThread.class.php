<?php
final class NewThread extends Controller {

	private $forum = null;
	
	private $title = "";
	private $message = "";
	
	private $titleError = -1;
	private $messageError = -1;
	
	private $preview = false;
	
	public function init() {
		$this->requireLogin = true;
		if(!$this->isLoggedIn()) {
			return;
		}
		
		$id = $_GET['forumId'] ?? null;
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
		
		$this->setForum($id);
		if($this->forum === null) {
			return;
		}
		
		if(isset($_POST['previewButton'])) {
			$this->titleError = $this->validateTitle();
			$this->messageError = $this->validateMessage();
			
			if($this->titleError === -1 && $this->messageError === -1) {
				$this->preview = true;
			}
		} else if(isset($_POST['submitButton'])) {
			$this->titleError = $this->validateTitle();
			$this->messageError = $this->validateMessage();
				
			if($this->titleError === -1 && $this->messageError === -1) {
				
			}
		}
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
	
	private function validateTitle() : int {
		$title = $_POST['titleInput'] ?? null;
		if($title === null || !is_string($title)) {
			return 0;
		}
		
		$title = trim($title);
		if($title === "") {
			return 0;
		}
		
		if(strlen($title) > 50) {
			return 1;
		}
		
		$this->title = $title;
		return -1;
	}
	
	private function setForum(int $id) {
		$stmt = $this->dbh->con->prepare('
			SELECT `id`, `name`, `description`, `threads`, `posts`, `locked` 
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
	
	public function isPreviewing() : bool {
		return $this->preview;
	}
	
	public function getTitleError() : int {
		return $this->titleError;
	}
	
	public function getMessageError() : int {
		return $this->messageError;
	}
	
	public function getTitle() : string {
		return $this->title;
	}
	
	public function getMessage() : string {
		return $this->message;
	}
	
	public function getForum() {
		return $this->forum;
	}
	
}
?>