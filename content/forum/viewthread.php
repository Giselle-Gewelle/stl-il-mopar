<?php
$cssImports = [ 'community/forum' ];
$navId = 'community';

$controllerClass = 'controller.community.forum.impl.ViewThread';
include('../../app/inc/app.php');

$forum = $ctrl->getForum();
$thread = $ctrl->getThread();

$title = ($thread === null || $forum === null ? 'Thread Not Found' : safe($thread->title));
$breadcrumb = [
	[ 'forum/forums', 'Forums' ]
];
if($thread === null || $forum === null) {
	array_push($breadcrumb, [ 'forum/viewthread', 'Thread Not Found' ]);
} else {
	array_push($breadcrumb, [ 'forum/viewforum', $forum->name, '?id='. $forum->id ]);
	array_push($breadcrumb, [ 'forum/viewthread', safe($thread->title), '?id='. $thread->id ]);
}

include('../../app/inc/content/header.php');
?>
<div id="content">
	<div class="inner">
		<h3><?php echo ($thread === null || $forum === null ? 'Thread Not Found' : safe($thread->title)); ?></h3>
		
		<?php
		if($thread === null || $forum === null) {
			?>
			<p>The thread you were looking for was not found.</p>
			<p><a href="<?php echo url('forum/forums'); ?>">Click here</a> to return to the forum index.</p>
			<?php
		} else {
			$posts = $ctrl->getPosts();
			
			$nav = '
			<div class="contentBox">
				<form class="pageNav" method="get" action="'. url('forum/viewthread') .'">
					<input type="hidden" name="id" value="'. $thread->id .'" />
					';
					$page = $ctrl->getPage();
					$pages = $ctrl->getPages();
						
					if($page > 1) {
						$nav .= '<a href="'. url('forum/viewthread', EXT, '?id='. $thread->id .'&page=1') .'">&lt;&lt;</a>&nbsp;
									<a href="'. url('forum/viewthread', EXT, '?id='. $thread->id .'&page='. ($page - 1)) .'">&lt;</a> &nbsp; ';
					} else {
						$nav .= '&lt;&lt;&nbsp; &lt; &nbsp; ';
					}
					
					$nav .= 'Page <input type="text" name="page" value="'. $page .'" /> of <strong>'. $pages .'</strong>';
					
					if($page < $pages) {
						$nav .= ' &nbsp; <a href="'. url('forum/viewthread', EXT, '?id='. $thread->id .'&page='. ($page + 1)) .'">&gt;</a>&nbsp;
								<a href="'. url('forum/viewthread', EXT, '?id='. $thread->id .'&page='. $pages) .'">&gt;&gt;</a>';
					} else {
						$nav .= ' &nbsp; &gt;&nbsp; &gt;&gt;';
					}
					
					$nav .= '
				</form>
				
				<div>
					<a href="'. url('forum/viewthread', EXT, '?id='. $thread->id .'&page='. $page) .'">Refresh</a>
					';
					if($ctrl->isLoggedIn()) {
						if(!$thread->locked || $ctrl->getUser()->mod) {
							$nav .= ' | <a href="'. url('forum/reply', EXT, '?threadId='. $thread->id) .'">Reply</a>';
						}
					}
					$nav .= '
				</div>
			</div>
			';
			
			echo $nav;
			?>
			
			<div id="thread">
				<?php
				foreach($posts as $post) {
					if($post->authorStaff) {
						echo '<div class="post staff';
					} else if($post->authorMod) {
						echo '<div class="post mod';
					} else {
						echo '<div class="post';
					}
					
					if(isset($_GET['post']) && $_GET['post'] === strval($post->id)) {
						echo ' highlight';
					}
					
					echo '">';
					?>
					<span id="post<?php echo $post->id; ?>" class="anchor"></span>
					
					<div class="user">
						<a class="username" href="<?php echo url('forum/user', EXT, '?id='. $post->authorId); ?>"><?php echo $post->author; ?></a>
						<span class="rights">
							<?php
							if($post->authorStaff) {
								echo 'Administrator';
							} else if($post->authorMod) {
								echo 'Moderator';
							}
							?>
						</span>
						
						<?php
						if($ctrl->isLoggedIn()) {
							if($ctrl->getUser()->id === $post->authorId || $ctrl->getUser()->staff) {
								echo '
								<span class="controls">
									<a href="'. url('forum/editpost', EXT, '?id='. $post->id) .'">Edit</a>
								</span>
								';
							}
							
							if($ctrl->getUser()->mod) {
								echo '
								<span class="controls">
									<a href="'. url('forum/mod/postaction', EXT, '?id='. $post->id .'&actionType=hide') .'">Hide</a>
								</span>
								';
							}
						}
						?>
					</div>
					
					<div class="message">
						<span class="date">
							<?php 
							echo date('d-M-Y H:i:s', strtotime($post->date)); 
							
							if($ctrl->isLoggedIn() && $ctrl->getUser()->mod) {
								echo ' ('. $post->authorIP .')';
							}
							
							if($post->lastEditDate !== null) {
								echo '<br />Edited on '. date('d-M-Y H:i:s', strtotime($post->lastEditDate)) .' by '. 
									'<a href="'. url('forum/user', EXT, '?id='. $post->lastEditorId) .'">'. $post->lastEditor .'</a>';
								
								if($ctrl->isLoggedIn() && $ctrl->getUser()->mod) {
									echo ' ('. $post->lastEditorIP .')';
								}
							}
							?>
						</span>
						
						<?php
						if($post->authorStaff) {
							echo nl2br($post->message);
						} else {
							echo nl2br(safe($post->message));
						}
						?>
					</div>
					<?php
					echo '</div>';
				}
				?>
			</div>
			
			<?php
			echo $nav;
		}
		?>
	</div>
</div>
<?php
include('../../app/inc/content/footer.php');
?>