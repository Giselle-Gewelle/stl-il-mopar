<?php
final class ChangePassword extends Controller {
	
	public function init() {
		$this->requireLogin = true;
		if(!$this->isLoggedIn()) {
			return;
		}
	}
	
}
?>