<?php

if (isset($comments['commentsData']) && isset($comments['commentsData']['comments']) && !empty($comments['commentsData']['comments'])) {
	foreach ($comments['commentsData']['comments'] as $key => $comment) {
		$comments['commentsData']['comments'][$key]['Comment']['formatted_created'] = $this -> Time -> format('F jS, Y h:i A', $comment['Comment']['created'], null);
	}
}

echo $this -> Js -> object($comments);
?>