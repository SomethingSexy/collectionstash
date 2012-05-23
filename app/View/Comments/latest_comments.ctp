<?php
foreach ($comments as $key => $comment) {
	echo '<li class="comment">';
	echo '<div class="comment-type">';
	if ($comment['EntityType']['type'] === 'stash') {
		echo $comment['EntityType']['Stash']['User']['username'] . '\'s ';
		echo 'Stash';
	} else if($comment['EntityType']['type'] === 'collectible') {
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
	}
	echo '</div>';

	echo '</li>';
}
?>