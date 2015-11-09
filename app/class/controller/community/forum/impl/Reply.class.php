<?php
importClass('controller.community.forum.ForumController');

final class Reply extends ForumController {
	
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
		
		$id = $_GET['threadId'] ?? null;
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
		
		$this->thread = $this->setThread($id);
		if($this->thread === null) {
			return;
		}
		
		$this->forum = $this->setForum($this->thread->forumId);
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
				if(!$this->createNewPost()) {
					$this->genericError = true;
				}
			}
		} else if(isset($_POST['cancelButton'])) {
			header('Location: '. url('forum/viewthread', EXT, '?id='. $this->thread->id));
			die('Please wait...');
		}
	}
	
	private function createNewPost() : bool {
		$c = $this->dbh->con;
		
		if(!$c->beginTransaction()) {
			return false;
		}
		
		$uid = $this->user->id;
		$username  = $this->user->username;
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = DateUtil::currentSqlDate();
		
		$stmt = $c->prepare('
			INSERT INTO `forum_posts` (
				`threadId`, `authorId`, `authorIP`, `date`, `message` 
			) VALUES (
				:tid, :authorId, :authorIP, :date, :msg
			);
		');
		$stmt->bindParam(':tid', $this->thread->id, PDO::PARAM_INT);
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
			UPDATE `forum_threads` 
			SET `posts` = (`posts` + 1), 
				`lastPostDate` = :date, 
				`lastPoster` = :uname, 
				`lastPosterId` = :uid, 
				`lastPosterIP` = :ip 
			WHERE `id` = :tid 
			LIMIT 1;
		');
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':uname', $username, PDO::PARAM_STR);
		$stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
		$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
		$stmt->bindParam(':tid', $this->thread->id, PDO::PARAM_INT);
		if(!$stmt->execute()) {
			$this->dbh->rollback();
			return false;
		}
		
		$stmt = $c->prepare('
			UPDATE `forum_forums` 
			SET `posts` = (`posts` + 1), 
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
		$stmt->bindParam(':threadTitle', $this->thread->title, PDO::PARAM_STR);
		$stmt->bindParam(':tid', $this->thread->id, PDO::PARAM_INT);
		$stmt->bindParam(':fid', $this->forum->id, PDO::PARAM_INT);
		if(!$stmt->execute()) {
			$this->dbh->rollback();
			return false;
		}
		
		$stmt = $c->prepare('
			UPDATE `user_accounts` 
			SET `posts` = (`posts` + 1)
			WHERE `id` = :uid 
			LIMIT 1;
		');
		$stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
		if(!$stmt->execute()) {
			$this->dbh->rollback();
			return false;
		}
		
		$c->commit();
		
		header('Location: '. url('forum/viewthread', EXT, '?id='. $this->thread->id .'&post='. $postId .'#post'. $postId));
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