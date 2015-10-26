<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="UTF-8" />
		
		<title><?php echo WEBSITE_TITLE; ?></title>
		
		<link rel="stylesheet" type="text/css" href="<?php echo URL; ?>/resources/css/layout.css" />
		
		<?php 
		$cssFiles = $cssImports ?? [];
		foreach($cssFiles as $cssFile) {
			echo '<link rel="stylesheet" type="text/css" href="'. URL .'/resources/css/'. $cssFile .'.css" />';
		}
		?>
	</head>
	
	<body>
		<div id="header">
			<div class="inner">
				<ul id="nav">
					<li><a href="<?php echo url('index'); ?>">Home</a></li>
					<li><a href="<?php echo url('account/manage'); ?>">Account</a>
						<ul>
							<li><a href="<?php echo url('account/manage'); ?>">Account Management</a></li>
							<li><a href="<?php echo url('account/create'); ?>">Create an Account</a></li>
						</ul>
					</li>
					<li><a href="<?php echo url('forum/forums'); ?>">Forum</a></li>
					<?php
					if($ctrl->isLoggedIn()) {
						if($ctrl->getUser()->staff) {
							echo '<li><a href="'. url('admin/center') .'">Admin</a></li>';
						}
						
						echo '<li><a href="'. url('account/logout') .'">Logout</a></li>';
					} else {
						echo '<li><a href="'. url('account/login') .'">Login</a></li>';
					}
					?>
				</ul>
				
				<h1>STL Modern Mopar</h1>
			</div>
		</div>
		
		<?php
		if(isset($title)) {
			echo '
			<div id="loc">
				<div class="inner">
					<h2>'. $title .'</h2>
					';
			
					if($ctrl->isLoggedIn()) {
						echo '
						<div id="session">
							Welcome, <strong>'. $ctrl->getUser()->username .'</strong>!
						</div>
						';
					}
					
					if(isset($breadcrumb)) {
						echo '
						<div id="breadcrumb">
							<a href="'. url('index') .'">Home</a> 
							';
							foreach($breadcrumb as $link) {
								echo ' &gt; <a href="'. url($link[0]) .'">'. $link[1] .'</a>';
							}
							echo '
						</div>
						';
					}
					echo '
				</div>
			</div>
			';
		}
		?>