<?php
$cssImports = [ 'account/login' ];
$title = 'Account Login';
$breadcrumb = [
	[ 'account/login', $title ]
];

$controllerClass = 'controller.account.Login';
include('../../app/inc/app.php');
include('../../app/inc/content/header.php');

$errorCode = $ctrl->getErrorCode();
?>
<div id="content">
	<div class="inner">
		<form id="loginForm" name="loginForm" autocomplete="off" method="post" action="<?php echo url('account/login'); ?>">
			<?php
			if($errorCode !== -1) {
				$errors = [
					'Please enter a valid username and password.',
					'Incorrect username or password, please try again.',
					'An unknown error has occurred, please try again.'
				];
				
				echo '<p class="error">'. $errors[$errorCode] .'</p>';
			}
			?>
			
			<div>
				<label for="loginUsername">Username:</label>
				<input type="text" id="loginUsername" name="loginUsername" maxlength="15" value="" />
			</div>
			
			<div class="section">
				<label for="loginPassword">Password:</label>
				<input type="password" id="loginPassword" name="loginPassword" maxlength="35" value="" />
			</div>
			
			<div class="section">
				<button id="loginSubmit" name="loginSubmit">Submit Login</button>
			</div>
		</form>
	</div>
</div>
<?php
include('../../app/inc/content/footer.php');
?>