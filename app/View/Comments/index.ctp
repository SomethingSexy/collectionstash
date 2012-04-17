<div id="latest-comments-component" class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('See what is being discussed...')
			?></h2>
		</div>
		<div class="component-view">
			<div class="latest-comments">
				<?php
                foreach ($comments as $key => $comment) {
                    echo '<div class="comment">';
                    echo '<div class="comment-type">';
                    if ($comment['CommentType']['type'] === 'stash') {
                        echo $comment['CommentType']['User']['username'] . '\'s ';
                        echo 'Stash';
                    }
                    echo '</div>';
                    echo '<div class="comment-text">';
                    if ($comment['CommentType']['type'] === 'stash') {
                        echo $this -> Html -> link($comment['Comment']['shorthand_comment'] . '...', array('admin' => false, 'controller' => 'stashs', 'action' => 'view', $comment['CommentType']['User']['username']));
                    }
                    echo '</div>';
                    echo '<div class="comment-info">';
                    echo '<span class="user">' . $comment['User']['username'] . '</span>';
                    echo '<span class="datetime">' . $comment['Comment']['formatted_created'] . '</span>';
                    echo '</div>';
                    echo '</div>';
                }
				?>
			</div>
		</div>
	</div>
</div>