<?php
$cssImports = [ 'community/forum' ];
$navId = 'community';

$controllerClass = 'controller.community.forum.Reply';
include('../../app/inc/app.php');

$forum = $ctrl->getForum();
$thread = $ctrl->getThread();

$title = ($thread === null || $forum === null ? 'Thread Not Found' : 'Reply to Thread');
$breadcrumb = [
	[ 'forum/forums', 'Forums' ]
];
if($thread === null || $forum === null) {
	array_push($breadcrumb, [ 'forum/viewthread', 'Thread Not Found' ]);
} else {
	array_push($breadcrumb, [ 'forum/viewforum', $forum->name, '?id='. $forum->id ]);
	array_push($breadcrumb, [ 'forum/viewthread', safe($thread->title), '?id='. $thread->id ]);
	array_push($breadcrumb, [ 'forum/reply', 'Reply to Thread', '?threadId='. $thread->id ]);
}

include('../../app/inc/content/header.php');
?>
<div id="content">
	<div class="inner">
		<?php
		if($thread === null || $forum === null) {
			?>
			<h3>Thread Not Found</h3>
			<p>The thread you were looking for was not found.</p>
			<p><a href="<?php echo url('forum/forums'); ?>">Click here</a> to return to the forum index.</p>
			<?php
		} else {
			if($thread->locked && !$ctrl->getUser()->mod) {
				?>
				<h3>Thread Locked</h3>
				<p>The thread you are trying to reply to has been locked by a moderator and can no longer be replied to.</p>
				<p><a href="<?php echo url('forum/viewthread', EXT, '?id='. $thread->id); ?>">Click here</a> to return to the thread.</p>
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
				
				$messageError = $ctrl->getMessageError();
				
				if($ctrl->getGenericError()) {
					?>
					<div class="contentBox">
						<strong class="error">An unknown error has occurred. Please try again or contact support.</strong>
					</div>
					
					<br />
					<?php 
				}
				
				if($messageError !== -1) {
					?>
					<div class="contentBox">
						<?php
						$messageErrors = [
							'Please input a valid message.',
							'Your message exceeded the maximum allowed length of '. ($ctrl->getUser()->staff ? 60000 : 5000) .' characters, please supply a different message.'
						];
						echo '<strong class="error">'. $messageErrors[$messageError] .'</strong>';
						?>
					</div>
					
					<br />
					<?php 
				}
				?>
				
				<form id="newPostForm" name="newPostForm" autocomplete="off" method="post" action="<?php echo url('forum/reply', EXT, '?threadId='. $thread->id); ?>">
					<div>
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
		}
		?>
	</div>
</div>
<?php
include('../../app/inc/content/footer.php');
?>