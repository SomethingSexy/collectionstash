<?php echo $this -> Html -> script('jquery.infinitescroll', array('inline' => false)); ?>
<div id="latest-comments-component" class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('See what is being discussed...')
			?></h2>
		</div>
		<div class="component-view">
			<div class="comments-container latest-comments">
						<?php echo '<div id="titles-nav" class="hidden">';
				echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
				echo '</div>';
		?>
				
				<ol id="comments" class="comments" data-page="2" data-page-count="<?php echo $pageCount; ?>">
					<?php
					foreach ($comments as $key => $comment) {
						echo '<li class="comment">';
						echo '<div class="comment-type">';
						if ($comment['EntityType']['type'] === 'stash') {
							echo $comment['EntityType']['Stash']['User']['username'] . '\'s ';
							echo 'Stash';
						} else if ($comment['EntityType']['type'] === 'collectible') {
							echo 'Collectible: ';
							echo $comment['EntityType']['Collectible']['name'];
						}
						echo '</div>';
						echo '<div class="comment-count">';
						echo $comment['EntityType']['comment_count'];
						if ($comment['EntityType']['comment_count'] === '1') {
							echo ' comment';
						} else {
							echo ' comments';
						}
						echo '</div>';
						echo '<div class="comment-info">';
						echo '<span class="user"><a href="/stashs/view/' . $comment['User']['username'] . ' ">' . $comment['User']['username'] . '</a></span>';
						echo '<span class="datetime">' . $comment['Comment']['formatted_created'] . '</span>';
						echo '</div>';
						echo '<div class="comment-text">';
						$commentText = str_replace('\n', "\n", $comment['Comment']['shorthand_comment']);
						$commentText = str_replace('\r', "\r", $commentText);
						$commentText = htmlspecialchars_decode($commentText, ENT_QUOTES);

						if ($comment['EntityType']['type'] === 'stash') {
							echo $this -> Html -> link($commentText . '...', array('admin' => false, 'controller' => 'stashs', 'action' => 'view', $comment['EntityType']['Stash']['User']['username']));
						} else if ($comment['EntityType']['type'] === 'collectible') {
							echo $this -> Html -> link($commentText . '...', array('admin' => false, 'controller' => 'collectibles', 'action' => 'view', $comment['EntityType']['Collectible']['id']));
						}
						echo '</div>';

						echo '</li>';
					}
					?>
				</ol>
			</div>
		</div>
	</div>
</div>

<script>
	$(function() {
		$('#comments').infinitescroll({
			nextSelector : "#titles-nav a",
			navSelector : "#titles-nav",
			itemSelector : ".comment",
			loading : {
				finishedMsg : "<em>All comments have been loaded!</em>",
				msgText : "<em>Loading the next set of comments.</em>",
			}
		});
	});

</script>