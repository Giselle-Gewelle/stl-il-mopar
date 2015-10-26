<?php
$cssImports = [ 'account/create' ];
$title = 'Create an Account';
$breadcrumb = [
	[ 'account/create', $title ]
];

$controllerClass = 'controller.account.CreateAccount';
include('../../app/inc/app.php');
include('../../app/inc/content/header.php');

$values = $ctrl->getValues();
$errors = $ctrl->getErrors();
?>
<div id="content">
	<div class="inner">
		<?php 
		if($ctrl->isSuccessful()) {
			?>
			<h2>Account Creation Successful</h2>
			<p>Your account has been created with the details you have provided.</p>
			<p>Please <a href="<?php echo url('account/login'); ?>">click here</a> to log into your account and get started!</p>
			<?php 
		} else {
			?>
			<form id="createForm" name="createForm" autocomplete="off" method="post" action="<?php echo url('account/create'); ?>">
				<div>
					<?php
					if($errors->cityState) {
						echo '<span class="label error">Please provide a valid city and state:</span>';
					} else {
						echo '<span class="label">Location:</span>';
					}
					?>
					<br />
					<select id="inputCreateState" name="inputCreateState">
						<option value=""<?php if($values->state === '') echo ' selected="selected"'; ?>>State</option>
						<option value="IL"<?php if($values->state === 'IL') echo ' selected="selected"'; ?>>Illinois</option>
						<option value="MO"<?php if($values->state === 'MO') echo ' selected="selected"'; ?>>Missouri</option>
						<option value="O"<?php if($values->state === 'O') echo ' selected="selected"'; ?>>Other</option>
					</select>
					<input type="text" id="inputCreateCity" name="inputCreateCity" maxlength="50" placeholder="City"
						value="<?php echo safe($values->city); ?>" />
				</div>
				
				<div class="section">
					<?php
					if($errors->username !== -1) {
						$usernameErrors = [
							'Please input a valid username',
							'Usernames must be between 1 and 15 characters in length. Please choose a new username',
							'The chosen username contains an invalid character. Please choose a new username',
							'That username is already registered to another user. Please choose a new username',
							'An unknown error has occurred, please try again or contact Website Support'
						];
						echo '<label for="inputCreateUsername" class="error">'. $usernameErrors[$errors->username] .':</label>';
					} else {
						echo '<label for="inputCreateUsername">Desired Username:</label>';
					}
					?>
					<br />
					<input type="text" id="inputCreateUsername" name="inputCreateUsername" maxlength="15"
						value="<?php echo safe($values->username); ?>" />
				</div>
				
				<div class="section">
					<?php
					if($errors->password !== -1) {
						$passwordErrors = [
							'Please input a valid password',
							'Passwords must be between 5 and 35 characters in length. Please choose a new password',
							'The chosen password contains an invalid character. Please choose a new password',
							'The passwords entered do not match'
						];
						echo '<label for="inputCreatePassword1" class="error">'. $passwordErrors[$errors->password] .':</label>';
					} else {
						echo '<label for="inputCreatePassword1">Desired Password:</label>';
					}
					?>
					<br />
					<input type="password" id="inputCreatePassword1" name="inputCreatePassword1" maxlength="35" />
				</div>
				
				<div class="section">
					<label for="inputCreatePassword2">Confirm Password:</label>
					<br />
					<input type="password" id="inputCreatePassword2" name="inputCreatePassword2" maxlength="35" />
				</div>
				
				<div class="section">
					<?php 
					if($errors->terms) {
						echo '<div class="error"><strong>You must agree to both the Terms of Use and Privacy Policy in order to create an account.</strong></div>';
					}
					?>
					<label for="inputCreateTerms">
						<input type="checkbox" id="inputCreateTerms" name="inputCreateTerms"
							<?php if($values->terms) echo ' checked="checked"' ?> /> 
						I have read and agree to the Terms of Use and Privacy Policy
					</label>
				</div>
				
				<div class="section">
					<button id="inputCreateSubmit" name="inputCreateSubmit">Submit Creation</button>
				</div>
			</form>
			<?php
		}
		?>
	</div>
</div>
<?php
include('../../app/inc/content/footer.php');
?>