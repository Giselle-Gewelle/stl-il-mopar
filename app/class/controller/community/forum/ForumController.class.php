<?php
abstract class ForumController extends Controller {
	
	public function setPost(int $id, bool $getHidden = false) {
		$stmt = $this->dbh->con->prepare('
			SELECT `p`.`id`, `p`.`authorId`, `p`.`date`, `p`.`message`, `p`.`threadId`, `p`.`hidden`, 
				`a`.`username` AS `author`, `a`.`staff` AS `authorStaff`, `a`.`mod` AS `authorMod`
			FROM `forum_posts` AS `p`
				JOIN `user_accounts` AS `a`
					ON `p`.`authorId` = `a`.`id`
			WHERE `p`.`id` = :pid
				'. (!$getHidden ? 'AND `p`.`hidden` = 0' : '') .'
			LIMIT 1;
		');
		$stmt->bindParam(':pid', $id, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_OBJ);
	
		if(!$result) {
			return;
		}
	
		return $result;
	}
	
	public final function setThread(int $id) {
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
	
		return $result;
	}
	
	public final function setForum(int $id) {
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
	
		return $result;
	}
	
}
?>