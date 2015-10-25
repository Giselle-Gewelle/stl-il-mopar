<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="UTF-8" />
		
		<title><?php echo WEBSITE_TITLE; ?></title>
		
		<link rel="stylesheet" type="text/css" href="resources/css/layout.css" />
		
		<script type="text/javascript" src="resources/js/lib/angular.min.js"></script>
	</head>
	
	<body>
		<div id="header">
			<div class="content">
				<h1><?php echo WEBSITE_TITLE; ?></h1>
			</div>
		</div>
		
		<div id="nav">
			<div class="content">
				<ul>
					<li><a href="<?php echo url('index'); ?>">Home</a></li>
					<li><a href="<?php echo url('account/manage'); ?>">Account</a>
						<ul>
							<li><a href="<?php echo url('account/create'); ?>">Create an Account</a></li>
							<li><a href="<?php echo url('account/manage'); ?>">Account Management</a></li>
						</ul>
					</li>
					<li><a href="<?php echo url('forum/forums'); ?>">Forums</a></li>
				</ul>
			</div>
		</div>
		
		<div id="body" class="content">