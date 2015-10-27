<?php
$cssImports = [ 'account/manage' ];
$title = 'Account Management';
$breadcrumb = [
	[ 'account/manage', $title ]
];
$navId = 'account';

$controllerClass = 'controller.account.Management';
include('../../app/inc/app.php');
include('../../app/inc/content/header.php');
?>
<div id="content">
	<div class="inner">
		<div class="box">
			<div class="header">
				Account Settings
			</div>
			
			<div class="content">
				<ul>
					<li><a href="<?php echo url('account/manage/changepass'); ?>">Change Your Password</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php
include('../../app/inc/content/footer.php');
?>