<?php
$cssImports = [ 'community/forum' ];
$title = 'Forums';
$breadcrumb = [
	[ 'forum/forums', $title ]
];
$navId = 'community';

$controllerClass = 'controller.community.forum.Forums';
include('../../app/inc/app.php');
include('../../app/inc/content/header.php');

$forums = $ctrl->getForums();
$lastList = -1;
?>
<div id="content">
	<div class="inner">
		<?php
		foreach($forums as $forum) {
			if($lastList !== $forum->listId) {
				if($lastList !== -1) {
					echo '</tbody></table>';
				}
				
				echo '
				<table class="forumList">
					<thead>
						<tr>
							<td class="title">'. $forum->listName .'</td>
							<td class="threads">Threads</td>
							<td class="posts">Posts</td>
							<td class="lastPost">Last Post</td>
						</tr>
					</thead>
					
					<tbody>
					';
				$lastList = $forum->listId;
			}
			
			echo '
			<tr class="forum">
				<td class="title">
					<a class="name" href="'. url('forum/viewforum', EXT, '?id='. $forum->id) .'">'. $forum->name .'</a>
					<div class="description">'. $forum->description .'</div>
				</td>
				<td class="threads">'. $forum->threads .'</td>
				<td class="posts">'. $forum->posts .'</td>
				<td class="lastPost">
					';
					if($forum->lastPostDate !== null) {
						echo '
						<a class="lastThreadName" href="'. url('forum/viewthread', EXT, '?id='. $forum->lastThreadId) .'">'. safe($forum->lastThread) .'</a><br />
						on '. date('d-M-Y H:i:s', strtotime($forum->lastPostDate)) .'<br />
						by <a href="'. url('forum/user', EXT, '?id='. $forum->lastPosterId) .'">'. $forum->lastPoster .'</a>
						';
					} else {
						echo 'No Posts';
					}
					echo '
				</td>
			</tr>
			';
		}
		
		echo '</tbody></table>';
		?>
	</div>
</div>
<?php
include('../../app/inc/content/footer.php');
?>