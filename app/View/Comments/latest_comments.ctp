<?php
foreach ($comments as $key => $comment) {
	echo '<div class="comment">';
	echo '<img src="/img/icon/avatar.png" alt="">';

	echo '<div class="content">';
	echo '<span class="commented-by">';
	echo '<a href="/stashs/view/' . $comment['User']['username'] . ' ">' . $comment['User']['username'] . '</a>';
	echo ' commented on ';
	if ($comment['EntityType']['type'] === 'stash') {
		echo $this -> Html -> link($comment['EntityType']['Stash']['User']['username'] . '\'s ' . 'Stash', array('admin' => false, 'controller' => 'stashs', 'action' => 'view', $comment['EntityType']['Stash']['User']['username']));
	} else if ($comment['EntityType']['type'] === 'collectible') {
		echo $this -> Html -> link('Collectible: ' . $comment['EntityType']['Collectible']['name'], array('admin' => false, 'controller' => 'collectibles', 'action' => 'view', $comment['EntityType']['Collectible']['id']));

	}
	echo '</span>';
	$commentText = str_replace('\n', "\n", $comment['Comment']['shorthand_comment']);
	$commentText = str_replace('\r', "\r", $commentText);
	$commentText = htmlspecialchars_decode($commentText, ENT_QUOTES);
	echo '<span class="comment-text">' . $commentText . '</span>';
	echo '<span class="actions">';
	echo '<a> </a>';
	echo '<span class="pull-right">' . $this -> Time -> format('F jS, Y h:i A', $comment['Comment']['formatted_created'], null) . '</span>';
	echo '</span>';

	echo '</div>';
	echo '</div>';
}
?>