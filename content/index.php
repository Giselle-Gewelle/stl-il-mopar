<!DOCTYPE html>

<?php
define("PAGE_TYPE", "content");
include("../app/inc/app.php");
?>

<html lang="en-US" ng-app="CmsApp" ng-controller="CmsCtrl as ctrl">
	<head>
		<meta charset="UTF-8" />
		
		<title><?php echo WEBSITE_TITLE; ?></title>
		
		<link rel="stylesheet" type="text/css" href="resources/css/layout.css" />
		
		<script type="text/javascript" src="resources/js/lib/angular.min.js"></script>
		
		<?php 
		$jsImports = [
			'main/Index',
			'account/AccountLanding', 'account/Create', 'account/Login',
			'community/CommunityLanding', 
			'support/SupportLanding', 'main/Terms'
		];
		
		foreach($jsImports as $import) {
			echo '<script type="text/javascript" src="resources/js/stlmodernmopar/ctrl/'. $import .'.js"></script>';
		}
		?>
		
		<script type="text/javascript" src="resources/js/stlmodernmopar/CmsCtrl.js"></script>
		<script type="text/javascript" src="resources/js/stlmodernmopar/CmsSvc.js"></script>
	</head>
	
	<body>
		<div id="busyOverlay" class="overlay" ng-show="ctrl.busy"></div>
		<div id="busy" class="popup" ng-show="ctrl.busy">
			<div class="box"></div>
			<div class="box"></div>
			<div class="box"></div>
			<div class="box"></div>
		</div>
		
		<div id="header">
			<div class="content">
				<h1><?php echo WEBSITE_TITLE; ?></h1>
			</div>
		</div>
		
		<div id="nav">
			<div class="content">
				<ul>
					<li ng-class="{ active: ctrl.isOnMainPage('index') }"><span ng-click="ctrl.setOpenPage('index')">Home</span></li>
					<li ng-class="{ active: ctrl.isOnMainPage('account') }"><span ng-click="ctrl.setOpenPage('account')">Account</span>
						<ul>
							<li ng-click="ctrl.setOpenPage('create')">Create an Account</li>
							<li ng-click="ctrl.setOpenPage('account')">Account Management</li>
						</ul>
					</li>
					<li ng-class="{ active: ctrl.isOnMainPage('community') }"><span ng-click="ctrl.setOpenPage('community')">Community</span></li>
					<li ng-class="{ active: ctrl.isOnMainPage('support') }"><span ng-click="ctrl.setOpenPage('support')">Support</span></li>
				</ul>
			</div>
		</div>
		
		<div id="body" class="content">
			<h2 ng-show="ctrl.hasTitle()">{{ ctrl.page.title }}</h2>
			
			<div class="loc" ng-show="ctrl.hasLocation()">
				<strong>Location:</strong>
				
				<div>
					<span ng-repeat="linkDetails in ctrl.page.location">
						<span class="link" ng-click="ctrl.setOpenPage(linkDetails[0])">{{ linkDetails[1] }}</span> &gt; 
					</span>
					{{ ctrl.page.title }}
				</div>
				
				<div class="clear"></div>
			</div>
			
			<div ng-show="ctrl.isOnPage('index')" ng-include="'view/main/index.html'"></div>
			<div ng-show="ctrl.isOnPage('terms')" ng-include="'view/main/terms.html'"></div>
			<div ng-show="ctrl.isOnPage('create')" ng-include="'view/account/create.html'"></div>
			<div ng-show="ctrl.isOnPage('login')" ng-include="'view/account/login.html'"></div>
		</div>
		
		<div id="footer" class="content">
			<div class="floatRight">
				<span class="link" ng-click="ctrl.setOpenPage('terms')">Terms of Use</span>
			</div>
			
			<div>
				<?php echo WEBSITE_TITLE; ?>
			</div>
		</div>
	</body>
</html>