<?php
$cssImports = [ 'community/forum' ];
$navId = 'community';

$controllerClass = 'controller.community.forum.impl.NewThread';
include('../../app/inc/app.php');

$forum = $ctrl->getForum();

$title = ($forum === null ? 'Forum Not Found' : 'Post a New Thread');
$breadcrumb = [
	[ 'forum/forums', 'Forums' ],
	[ 'forum/viewforum', ($forum === null ? 'Forum Not Found' : $forum->name), ($forum === null ? '' : '?id='. $forum->id) ]
];
if($forum !== null) {
	array_push($breadcrumb, [ 'forum/newthread', 'Post a New Thread', '?forumId='. $forum->id ]);
}

include('../../app/inc/content/header.php');
?>
<div id="content">
	<div class="inner">
		<?php
		if($forum === null) {
			?>
			<h3>Forum Not Found</h3>
			<p>The forum you were looking for was not found.</p>
			<p><a href="<?php echo url('forum/forums'); ?>">Click here</a> to return to the forum index.</p>
			<?php
		} else {
			if($ctrl->isPreviewing()) {
				?>
				<div id="thread">
					<?php
					if($ctrl->getUser()->staff) {
						echo '<div class="post staff">';
					} else if($ctrl->getUser()->mod) {
						echo '<div class="post mod">';
					} else {
						echo '<div class="post">';
					}
					?>
						<div class="user">
							<strong><?php echo $ctrl->getUser()->username; ?></strong>
							<span class="rights">
								<?php
								if($ctrl->getUser()->staff) {
									echo 'Administrator';
								} else if($ctrl->getUser()->mod) {
									echo 'Moderator';
								}
								?>
							</span>
						</div>
						
						<div class="message">
							<span class="date">
								<?php echo date('d-M-Y H:i:s'); ?>
							</span>
							
							<?php
							if($ctrl->getUser()->staff) {
								echo nl2br($ctrl->getMessage());
							} else {
								echo nl2br(safe($ctrl->getMessage()));
							}
							?>
						</div>
					</div>
				</div>
				
				<br />
				<?php
			}
			
			$titleError = $ctrl->getTitleError();
			$messageError = $ctrl->getMessageError();
			
			if($ctrl->getGenericError()) {
				?>
				<div class="contentBox">
					<strong class="error">An unknown error has occurred. Please try again or contact support.</strong>
				</div>
				
				<br />
				<?php 
			}
			
			if($titleError !== -1 || $messageError !== -1) {
				?>
				<div class="contentBox">
					<?php
					if($titleError !== -1) {
						$titleErrors = [
							'Please input a valid title.',
							'Your title exceeded the maximum allowed length of 50 characters, please supply a different title.'
						];
						echo '<strong class="error">'. $titleErrors[$titleError] .'</strong>';
					}
					if($messageError !== -1) {
						if($titleError !== -1) {
							echo '<br /><br />';
						}
						$messageErrors = [
							'Please input a valid message.',
							'Your message exceeded the maximum allowed length of '. ($ctrl->getUser()->staff ? 60000 : 5000) .' characters, please supply a different message.'
						];
						echo '<strong class="error">'. $messageErrors[$messageError] .'</strong>';
					}
					?>
				</div>
				
				<br />
				<?php 
			}
			?>
			<form id="newThreadForm" name="newThreadForm" autocomplete="off" method="post" action="<?php echo url('forum/newthread', EXT, '?forumId='. $forum->id); ?>">
				<div>
					<label for="titleInput">Title:</label>
					<input type="text" id="titleInput" name="titleInput" value="<?php echo safe($ctrl->getTitle()); ?>" maxlength="50" />
				</div>
				
				<div class="section">
					<label for="messageInput">Message:</label>
					<textarea id="messageInput" name="messageInput" maxlength="<?php echo ($ctrl->getUser()->staff ? '60000' : '5000'); ?>"><?php echo ($ctrl->getUser()->staff ? $ctrl->getMessage() : safe($ctrl->getMessage())); ?></textarea>
				</div>
				
				<div class="section">
					<button id="submitButton" name="submitButton">Submit</button>
					&nbsp;
					<button id="previewButton" name="previewButton">Preview</button>
					&nbsp;
					<button id="cancelButton" name="cancelButton">Cancel</button>
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