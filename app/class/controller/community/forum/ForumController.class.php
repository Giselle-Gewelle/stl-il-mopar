<?php
abstract class ForumController extends Controller {
	
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
	
		return $result;
	}
	
}
?>