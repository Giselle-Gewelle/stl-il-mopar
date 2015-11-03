<?php
final class NewThread extends Controller {

	private $forum = null;
	
	private $title = "";
	private $message = "";
	
	private $titleError = -1;
	private $messageError = -1;
	
	private $preview = false;
	private $genericError = false;
	
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
				if(!$this->createNewThread()) {
					$this->genericError = true;
				}
			}
		}
	}
	
	private function createNewThread() : bool {
		$c = $this->dbh->con;
		
		if(!$c->beginTransaction()) {
			return false;
		}
		
		$uid = $this->user->id;
		$username  = $this->user->username;
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = DateUtil::currentSqlDate();
		
		$stmt = $c->prepare('
			INSERT INTO `forum_threads` (
				`forumId`, `title`, `author`, `authorId`, `authorIP`, `date`, `lastPostDate`, `lastPoster`, `lastPosterId`, `lastPosterIP`
			) VALUES (
				:fid, :title, :author, :authorId, :authorIP, :date, :date, :author, :authorId, :authorIP
			);
		');
		$stmt->bindParam(':fid', $this->forum->id, PDO::PARAM_INT);
		$stmt->bindParam(':title', $this->title, PDO::PARAM_STR);
		$stmt->bindParam(':author', $username, PDO::PARAM_STR);
		$stmt->bindParam(':authorId', $uid, PDO::PARAM_INT);
		$stmt->bindParam(':authorIP', $ip, PDO::PARAM_STR);
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		if(!$stmt->execute()) {
			$this->dbh->rollback();
			return false;
		}
		
		$threadId = $c->lastInsertId();
		if(!is_numeric($threadId) || (int) $threadId < 1) {
			$this->dbh->rollback();
			return false;
		}
		
		$threadId = (int) $threadId;
		
		$stmt = $c->prepare('
			INSERT INTO `forum_posts` (
				`threadId`, `author`, `authorId`, `authorIP`, `date`, `message` 
			) VALUES (
				:tid, :author, :authorId, :authorIP, :date, :msg
			);
		');
		$stmt->bindParam(':tid', $threadId, PDO::PARAM_INT);
		$stmt->bindParam(':author', $username, PDO::PARAM_STR);
		$stmt->bindParam(':authorId', $uid, PDO::PARAM_INT);
		$stmt->bindParam(':authorIP', $ip, PDO::PARAM_STR);
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':msg', $this->message, PDO::PARAM_STR);
		if(!$stmt->execute()) {
			$this->dbh->rollback();
			return false;
		}
		
		$postId = $c->lastInsertId();
		if(!is_numeric($postId) || (int) $postId < 1) {
			$this->dbh->rollback();
			return false;
		}
		
		$stmt = $c->prepare('
			UPDATE `forum_forums` 
			SET `threads` = (`threads` + 1), 
				`posts` = (`posts` + 1), 
				`lastPostDate` = :date, 
				`lastPoster` = :uname, 
				`lastPosterId` = :uid,
				`lastPostId` = :pid,
				`lastThread` = :threadTitle,
				`lastThreadId` = :tid 
			WHERE `id` = :fid 
			LIMIT 1;
		');
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':uname', $username, PDO::PARAM_STR);
		$stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
		$stmt->bindParam(':pid', $postId, PDO::PARAM_INT);
		$stmt->bindParam(':threadTitle', $this->title, PDO::PARAM_STR);
		$stmt->bindParam(':tid', $threadId, PDO::PARAM_INT);
		$stmt->bindParam(':fid', $this->forum->id, PDO::PARAM_INT);
		if(!$stmt->execute()) {
			$this->dbh->rollback();
			return false;
		}
		
		$stmt = $c->prepare('
			UPDATE `user_accounts` 
			SET `posts` = (`posts` + 1), 
				`threads` = (`threads` + 1)
			WHERE `id` = :uid 
			LIMIT 1;
		');
		$stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
		if(!$stmt->execute()) {
			$this->dbh->rollback();
			return false;
		}
		
		$c->commit();
		
		header('Location: '. url('forum/viewthread', EXT, '?id='. $threadId));
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
	
	public function getGenericError() : bool {
		return $this->genericError;
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