<?php
$cssImports = [ 'account/management/changepass' ];
$title = 'Change Password';
$breadcrumb = [
	[ 'account/manage', 'Account Management' ],
	[ 'account/manage/changepass', $title ]
];
$navId = 'account';

$controllerClass = 'controller.account.management.ChangePassword';
include('../../../app/inc/app.php');
include('../../../app/inc/content/header.php');
?>
<div id="content">
	<div class="inner">
		<?php
		if($ctrl->isSuccessful()) {
			?>
			<h3>Password Change Request Accepted</h3>
			<p>The password for your account has been successfully changed.</p>
			<p>Please <a href="<?php echo url('account/manage'); ?>">click here</a> to return to the Account Management menu.</p>
			<?php
		} else {
			$errorCode = $ctrl->getErrorCode();
			if($errorCode !== -1) {
				$errors = [
					'Please enter valid values into the current, new, and confirm password fields.',
					'Passwords must be between 5 and 35 characters in length. Please choose a new password.',
					'The chosen password contains an invalid character. Please choose a new password.',
					'The passwords entered do not match.',
					'The password entered into the current password field does not match our records.',
					'The password entered into the current password and new password fields are the same. Please choose a new password.',
					'An unknown error has occurred, please try again.'
				];
				
				echo '
				<div>
					<p class="error"><strong>'. $errors[$errorCode] .'</strong></p>
					<hr />
				</div>
				';
			}
			?>
			<form id="passwordChangeForm" name="passwordChangeForm" autocomplete="off" method="post" action="<?php echo url('account/manage/changepass'); ?>">
				<div id="right">
					<p>Use this form to change the password you use to log into your website account.</p>
					<p>Passwords must be between 5 and 35 characters in length and <strong>are</strong> case-sensitive.</p>
					<p>Passwords may only contain numbers and letters (lower and uppercase).</p>
				</div>
				
				<div id="left">
					<div>
						<label for="currentPassword">Current Password:</label>
						<input type="password" id="currentPassword" name="currentPassword" maxlength="35" />
					</div>
					
					<div class="section">
						<div>
							<label for="password1">Desired Password:</label>
							<input type="password" id="password1" name="password1" maxlength="35" />
						</div>
						
						<div>
							<label for="password2">Confirm Password:</label>
							<input type="password" id="password2" name="password2" maxlength="35" />
						</div>
					</div>
					
					<div class="section">
						<button id="passwordChangeSubmit" name="passwordChangeSubmit">Submit</button>
					</div>
				</div>
				
				<div class="clear"></div>
			</form>
			<?php
		}
		?>
	</div>
</div>
<?php
include('../../../app/inc/content/footer.php');
?>