<?php
importClass('controller.community.forum.ForumController');

final class PostAction extends ForumController {
	
	private $actionType = '';
	private $unauth = false;
	private $post = null;
	
	private $genericError = false;
	private $reasonError = -1;
	private $reason = '';
	private $previewing = false;
	
	public function init() {
		$this->requireLogin = true;
		if(!$this->isLoggedIn()) {
			return;
		}
		
		if(!$this->user->mod) {
			$this->unauth = true;
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
		
		$actionType = $_GET['actionType'] ?? null;
		if($actionType === null || !is_string($actionType) || ($actionType !== 'hide' && $actionType !== 'show')) {
			$actionType = 'hide';
		}
		
		$this->actionType = $actionType;
		
		$this->post = $this->setPost($id, true);
		if($this->post === null) {
			return;
		}
		
		if($actionType === 'hide' && $this->post->hidden) {
			$this->post = null;
			return;
		}
		if($actionType === 'show' && !$this->post->hidden) {
			$this->post = null;
			return;
		}
		if($actionType === 'show' && !$this->user->staff) {
			$this->post = null;
			return;
		}
		
		if(isset($_POST['previewButton'])) {
			$this->reasonError = $this->validateReason();
				
			if($this->reasonError === -1) {
				$this->previewing = true;
			}
		} else if(isset($_POST['submitButton'])) {
			$this->reasonError = $this->validateReason();
		
			if($this->reasonError === -1) {
				if(!$this->submitReport()) {
					$this->genericError = true;
				}
			}
		} else if(isset($_POST['cancelButton'])) {
			header('Location: '. url('forum/viewthread', EXT, '?id='. $this->post->threadId));
			die('Please wait...');
		}
	}
	
	private function validateReason() : int {
		$reason = $_POST['reasonInput'] ?? null;
		if($reason === null || !is_string($reason)) {
			return 0;
		}
		
		$reason = trim($reason);
		if($reason === "") {
			return 0;
		}
		
		if(strlen($reason) > 1000) {
			return 1;
		}
		
		$this->reason = $reason;
		return -1;
	}
	
	public function getReasonError() : int {
		return $this->reasonError;
	}
	
	public function getGenericError() : bool {
		return $this->genericError;
	}
	
	public function getReason() : string {
		return $this->reason;
	}
	
	public function isPreviewing() : bool {
		return $this->previewing;
	}
	
	public function getActionType() : string {
		return $this->actionType;
	}
	
	public function isUnauth() : bool {
		return $this->unauth;
	}
	
	public function getPost() {
		return $this->post;
	}
	
}
?>