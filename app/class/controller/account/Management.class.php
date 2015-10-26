<?php
final class Management extends Controller {
	
	public function init() {
		$this->requireLogin = true;
		if(!$this->isLoggedIn()) {
			return;
		}
	}
	
}
?>