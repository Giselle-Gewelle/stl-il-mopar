<?php
$cssImports = [ 'community/forum' ];

$controllerClass = 'controller.community.forum.impl.mod.PostAction';
include('../../../app/inc/app.php');

$post = null;
$actionType = $ctrl->getActionType();

if($ctrl->isUnauth()) {
	$navId = 'community';
	$title = 'Unauthorized';
	$breadcrumb = [
		[ 'forum/forums', 'Forums' ],
		[ 'forum/mod/editpost', $title ]
	];
} else {
	$post = $ctrl->getPost();
	
	$navId = 'mod';
	
	if($post === null) {
		$title = 'Post Not Found';
		$breadcrumb = [
			[ 'forum/forums', 'Forums' ],
			[ 'forum/mod/editpost', $title ]
		];
	} else {
		$title = ucwords($actionType) .' Post';
		$breadcrumb = [
			[ 'forum/forums', 'Forums' ],
			[ 'forum/mod/postaction', $title, '?id='. $post->id .'&actionType='. $actionType ]
		];
	}
}

include('../../../app/inc/content/header.php');
?>
<div id="content">
	<div class="inner">
		<?php
		if($ctrl->isUnauth()) {
			echo '
			<h3>Unauthorized</h3>
			<p>You are not authorized to access this page.</p>
			<p><a href="'. url('forum/forums') .'">Click here</a> to return to the forum index.</p>
			';
		} else {
			if($post === null) {
				echo '
				<h3>Post Not Found</h3>
				<p>The post you were looking for was not found.</p>
				<p><a href="'. url('forum/forums') .'">Click here</a> to return to the forum index.</p>
				';
			} else {
				?>
				<h3><?php echo ucwords($actionType); ?> Post</h3>
				
				<p><strong>You have selected the following post:</strong></p>
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
								<?php echo date('d-M-Y H:i:s', strtotime($post->date)); ?>
							</span>
							
							<?php
							if($post->authorStaff) {
								echo nl2br($post->message);
							} else {
								echo nl2br(safe($post->message));
							}
							?>
						</div>
					</div>
				</div>
				
				<br /><br />
				
				<form id="postActionForm" name="postActionForm" class="messageForm" method="post" 
						action="<?php echo url('forum/mod/postaction', EXT, '?id='. $post->id .'&actionType='. $actionType); ?>">
					<div>
						<label for="reasonInput">Please input a reason for <?php echo $actionType === 'hide' ? 'hiding' : 'showing'; ?> this post:</label>
						<textarea id="reasonInput" name="reasonInput" maxlength="1000"><?php echo safe($ctrl->getReason()); ?></textarea>
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
include('../../../app/inc/content/footer.php');
?>