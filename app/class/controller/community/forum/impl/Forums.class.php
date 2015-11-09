<?php
importClass('controller.community.forum.ForumController');

final class Forums extends ForumController {
	
	private $forums = [];
	
	public function init() {
		$stmt = $this->dbh->con->prepare('
			SELECT `f`.`id`, `f`.`name`, `f`.`description`, `f`.`threads`, `f`.`posts`, `f`.`lastPostDate`, `f`.`lastPoster`, `f`.`lastPosterId`, `f`.`lastThread`, `f`.`lastThreadId`, 
				`l`.`id` AS `listId`, `l`.`name` AS `listName` 
			FROM `forum_forums` AS `f` 
				JOIN `forum_lists` AS `l` 
					ON `f`.`listId` = `l`.`id` 
			ORDER BY `l`.`position` ASC, 
				`f`.`position` ASC;
		');
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if(!$results) {
			return;
		}
		
		$this->forums = $results;
	}
	
	public function getForums() : array {
		return $this->forums;
	}
	
}
?>