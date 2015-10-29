<?php
$cssImports = [ 'community/forum' ];
$navId = 'community';

$controllerClass = 'controller.community.forum.ViewForum';
include('../../app/inc/app.php');

$forum = $ctrl->getForum();

$title = ($forum === null ? 'Forum Not Found' : $forum->name);
$breadcrumb = [
	[ 'forum/forums', 'Forums' ],
	[ 'forum/viewforum', $title, ($forum === null ? '' : '?id='. $forum->id) ]
];

include('../../app/inc/content/header.php');
?>
<div id="content">
	<div class="inner">
		<h3><?php echo ($forum === null ? 'Forum Not Found' : $forum->name); ?></h3>
		
		<?php
		if($forum === null) {
			?>
			<p>The forum you were looking for was not found.</p>
			<p><a href="<?php echo url('forum/forums'); ?>">Click here</a> to return to the forum index.</p>
			<?php
		} else {
			$threads = $ctrl->getThreads();
			?>
			
			<div class="contentBox">
				<a href="<?php echo url('forum/viewforum', EXT, '?id='. $forum->id); ?>">Refresh</a>
				<?php
				if($ctrl->isLoggedIn()) {
					if(!$forum->locked || $ctrl->getUser()->mod) {
						echo ' | <a href="'. url('forum/newthread', EXT, '?forumId='. $forum->id) .'">Post a New Thread</a>';
					}
				}
				?>
			</div>
			
			<?php
			if($threads === null) {
				?>
				<div class="contentBox">There are currently no threads in this forum.</div>
				<?php
			} else {
				
			}
		}
		?>
	</div>
</div>
<?php
include('../../app/inc/content/footer.php');
?>