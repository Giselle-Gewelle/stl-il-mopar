<?php
$cssImports = [ 'community/forum' ];
$navId = 'community';

$controllerClass = 'controller.community.forum.impl.EditPost';
include('../../app/inc/app.php');

$post = $ctrl->getPost();
$forum = $ctrl->getForum();
$thread = $ctrl->getThread();

$title = ($post === null || $thread === null || $forum === null ? 'Thread Not Found' : 'Reply to Thread');
$breadcrumb = [
	[ 'forum/forums', 'Forums' ]
];
if($post === null || $thread === null || $forum === null) {
	array_push($breadcrumb, [ 'forum/editpost', 'Post Not Found' ]);
} else {
	array_push($breadcrumb, [ 'forum/viewforum', $forum->name, '?id='. $forum->id ]);
	array_push($breadcrumb, [ 'forum/viewthread', safe($thread->title), '?id='. $thread->id ]);
	array_push($breadcrumb, [ 'forum/editpost', 'Edit Post', '?id='. $post->id ]);
}

include('../../app/inc/content/header.php');
?>
<div id="content">
	<div class="inner">
		<?php
		if($post === null || $thread === null || $forum === null) {
			?>
			<h3>Post Not Found</h3>
			<p>The post you were looking for was not found.</p>
			<p><a href="<?php echo url('forum/forums'); ?>">Click here</a> to return to the forum index.</p>
			<?php
		} else {
			if($post->authorId !== $ctrl->getUser()->id && !$ctrl->getUser()->staff) {
				?>
				<h3>Unauthorized</h3>
				<p>The post you are trying to edit does not belong to you.</p>
				<p><a href="<?php echo url('forum/viewthread', EXT, '?id='. $thread->id); ?>">Click here</a> to return to the thread.</p>
				<?php
			} else {
				if($ctrl->isPreviewing()) {
					?>
					<div id="thread">
						<?php
						if($post->authorStaff) {
							echo '<div class="post staff">';
						} else if($post->authorMod) {
							echo '<div class="post mod">';
						} else {
							echo '<div class="post">';
						}
						?>
							<div class="user">
								<strong><?php echo $post->author; ?></strong>
								<span class="rights">
									<?php
									if($post->authorStaff) {
										echo 'Administrator';
									} else if($post->authorMod) {
										echo 'Moderator';
									}
									?>
								</span>
							</div>
							
							<div class="message">
								<span class="date">
									<?php echo date('d-M-Y H:i:s', strtotime($post->date)); ?><br />
									Edited on <?php echo date('d-M-Y H:i:s'); ?> by <?php echo $ctrl->getUser()->username; ?>
								</span>
								
								<?php
								if($post->authorStaff) {
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
				
				<form id="editPostForm" name="editPostForm" autocomplete="off" method="post" action="<?php echo url('forum/editpost', EXT, '?id='. $post->id); ?>">
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