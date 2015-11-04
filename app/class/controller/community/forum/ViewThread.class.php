<?php
final class ViewThread extends Controller {

	const POSTS_PER_PAGE = 10;
	
	private $thread = null;
	private $forum = null;
	private $posts = null;
	
	private $page = 1;
	private $pages = 1;
	
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
		
		$this->setThread($id);
		if($this->thread === null) {
			return;
		}
		
		$this->setForum($this->thread->forumId);
		if($this->forum === null) {
			return;
		}
		
		$this->setPosts($this->thread->id);
	}
	
	private function setPosts(int $threadId) {
		$page = $_GET['page'] ?? 1;
		if(!is_numeric($page)) {
			$page = 1;
		}
		
		$page = (int) $page;
		if($page < 1 || $page > PHP_INT_MAX) {
			$page = 1;
		}
		
		$pages = ceil($this->thread->posts / self::POSTS_PER_PAGE);
		if($page > $pages) {
			$page = $pages;
		}
		
		$start = $page * self::POSTS_PER_PAGE - self::POSTS_PER_PAGE;
		
		$this->page = $page;
		$this->pages = $pages;
		
		$stmt = $this->dbh->con->prepare('
			SELECT `p`.`id`, `p`.`authorId`, `p`.`date`, `p`.`message`, 
				`a`.`username` AS `author`, `a`.`staff` AS `authorStaff`, `a`.`mod` AS `authorMod` 
			FROM `forum_posts` AS `p` 
				JOIN `user_accounts` AS `a` 
					ON `p`.`authorId` = `a`.`id` 
			WHERE `p`.`threadId` = :tid 
				AND `p`.`hidden` = 0 
			ORDER BY `date` ASC 
			LIMIT '. $start .','. self::POSTS_PER_PAGE .';
		');
		$stmt->bindParam(':tid', $threadId, PDO::PARAM_INT);
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if(!$results) {
			return;
		}
		
		$this->posts = $results;
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
	
	public function getThread() {
		return $this->thread;
	}

	public function getForum() {
		return $this->forum;
	}
	
	public function getPosts() {
		return $this->posts;
	}
	
	public function getPage() : int {
		return $this->page;
	}
	
	public function getPages() : int {
		return $this->pages;
	}
	
}
?>