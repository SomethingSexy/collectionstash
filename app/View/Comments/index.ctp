<div id="latest-comments-component" class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('See what is being discussed...')
			?></h2>
		</div>
		<div class="component-view">
			<div class="comments-container latest-comments">
				<ol id="comments" class="comments" data-page="1">
					<?php
					foreach ($comments as $key => $comment) {
						echo '<li class="comment">';
						echo '<div class="comment-type">';
						if ($comment['EntityType']['type'] === 'stash') {
							echo $comment['EntityType']['User']['username'] . '\'s ';
							echo 'Stash';
						}
						echo '</div>';
						echo '<div class="comment-count">';
						echo $comment['EntityType']['comment_count'];
						if ($comment['EntityType']['comment_count'] === 1) {
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
							echo $this -> Html -> link($commentText . '...', array('admin' => false, 'controller' => 'stashs', 'action' => 'view', $comment['EntityType']['User']['username']));
						}
						echo '</div>';

						echo '</li>';
					}
					?>
				</ol>
				<div id="loadmoreajaxloader" class="comments-loader">
					<span>Loading Comments</span>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(function() {
		$(window).data('ajaxready', true).scroll(function(e) {

			if($(window).data('ajaxready') === false)
				return;
			// alert($(window).scrollTop() + ' ' + ($(document).height() - $(window).height()));
			// The second part is done to fix a FF issue where it reports it as one pixel more than it should
			if($(window).scrollTop() >= ($(document).height() - $(window).height()) || $(window).scrollTop() == ($(document).height() - $(window).height() - 1)) {
				$('div#loadmoreajaxloader').css('visibility', 'visible');
				$(window).data('ajaxready', false);

				$.ajax({
					cache : false,
					url : '/comments/latestComments/page:' + $('#comments').attr('data-page'),
					success : function(html) {
						if(html) {
							$('#postswrapper').append(html);
							$('div#loadmoreajaxloader').css('visibility', 'hidden');
						} else {
							// $('div#loadmoreajaxloader').html();
						}

						$('#comments').attr('data-page', parseInt($('#comments').attr('data-page')) + 1);

						$(window).data('ajaxready', true);

						alert($('#comments').attr('data-page'));
					}
				});
			}
		});
	});

</script>