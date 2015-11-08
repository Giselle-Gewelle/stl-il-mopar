<?php
importClass('controller.community.forum.ForumController');

final class ViewThread extends ForumController {

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
		
		$this->thread = $this->setThread($id);
		if($this->thread === null) {
			return;
		}
		
		$this->forum = $this->setForum($this->thread->forumId);
		if($this->forum === null) {
			return;
		}
		
		$this->setPosts($this->thread->id);
	}
	
	private function setPosts(int $threadId) {
		$postSearch = $_GET['post'] ?? null;
		$before = -1;
		if($postSearch !== null && is_numeric($postSearch) && (int) $postSearch > 0) {
			$postSearch = (int) $postSearch;
			
			$stmt = $this->dbh->con->prepare('
				SELECT `date` 
				FROM `forum_posts` 
				WHERE `id` = :pid 
					AND `threadId` = :tid 
					AND `hidden` = 0 
				LIMIT 1;
			');
			$stmt->bindParam(':pid', $postSearch, PDO::PARAM_INT);
			$stmt->bindParam(':tid', $this->thread->id, PDO::PARAM_INT);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_OBJ);
			
			if($result !== false && $result->date !== null) {
				$stmt = $this->dbh->con->prepare('
					SELECT COUNT(`id`) AS `count` 
					FROM `forum_posts` 
					WHERE `threadId` = :tid 
						AND `hidden` = 0 
						AND `id` < :pid;
				');
				$stmt->bindParam(':tid', $this->thread->id, PDO::PARAM_INT);
				$stmt->bindParam(':pid', $postSearch, PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_OBJ);
				
				if($result !== false) {
					$before = $result->count;
				}
			}
		}
		
		$page = 1;
		
		if($before === -1) {
			$page = $_GET['page'] ?? 1;
			if(!is_numeric($page)) {
				$page = 1;
			}
			
			$page = (int) $page;
			if($page < 1 || $page > PHP_INT_MAX) {
				$page = 1;
			}
		} else {
			$before++;
			$page = ceil($before / self::POSTS_PER_PAGE);
		}
			
		$pages = ceil($this->thread->posts / self::POSTS_PER_PAGE);
		if($page > $pages) {
			$page = $pages;
		}
		
		$start = $page * self::POSTS_PER_PAGE - self::POSTS_PER_PAGE;
		
		$this->page = $page;
		$this->pages = $pages;
		
		$stmt = $this->dbh->con->prepare('
			SELECT `p`.`id`, `p`.`authorId`, `p`.`authorIP`, `p`.`date`, `p`.`message`, `p`.`lastEditDate`, `p`.`lastEditor`, `p`.`lastEditorId`, `p`.`lastEditorIP`, 
				`a`.`username` AS `author`, `a`.`staff` AS `authorStaff`, `a`.`mod` AS `authorMod` 
			FROM `forum_posts` AS `p` 
				JOIN `user_accounts` AS `a` 
					ON `p`.`authorId` = `a`.`id` 
			WHERE `p`.`threadId` = :tid 
				AND `p`.`hidden` = 0 
			ORDER BY `id` ASC 
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