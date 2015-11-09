<?php
importClass('controller.community.forum.ForumController');

final class ViewForum extends ForumController {
	
	private $forum = null;
	private $threads = null;
	
	public function init() {
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
		
		$this->forum = $this->setForum($id);
		if($this->forum === null) {
			return;
		}
		
		$this->setThreads($id);
	}
	
	private function setThreads(int $id) {
		if($this->forum === null) {
			return;
		}
		
		$stmt = $this->dbh->con->prepare('
			SELECT `id`, `title`, `author`, `authorId`, `date`, `posts`, `lastPostDate`, `lastPoster`, `lastPosterId`, `locked` 
			FROM `forum_threads` 
			WHERE `forumId` = :fid 
				AND `hidden` = 0 
			ORDER BY `lastPostDate` DESC;
		');
		$stmt->bindParam(':fid', $id, PDO::PARAM_INT);
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if(!$results) {
			return;
		}
		
		$this->threads = $results;
	}
	
	public function getThreads() {
		return $this->threads;
	}
	
	public function getForum() {
		return $this->forum;
	}
	
}
?>