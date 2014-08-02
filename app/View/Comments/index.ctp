<?php echo $this -> Minify -> script('jquery.infinitescroll', array('inline' => false)); ?>
<?php echo $this -> Html -> script('/bower_components/blockies/blockies', array('inline' => false)); ?>
			<h2><?php echo __('See what is being discussed...')
			?></h2>
<div id="latest-comments-component" class="widget">
		<div class="widget-header">
			<i class="fa fa-comments"></i><h3>Discussion</h3>
		</div>
		<div class="widget-content">
			
		
			<div id="comments" class="comments-container latest-comments">
			<?php echo '<div id="titles-nav" class="hidden">';
				echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
				echo '</div>';
		?>
				
				<div class="comments">
					<?php
					foreach ($comments as $key => $comment) {
						echo '<div class="comment">';
						// echo '<img src="/img/icon/avatar.png" alt="">';
						echo '<div class="blockie" data-seed="' . $comment['User']['username']. '"></div>';

						echo '<div class="content">';
						echo '<span class="commented-by">';
						echo '<a href="/profile/' . $comment['User']['username'] . ' ">' . $comment['User']['username'] . '</a>';
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
				</div>
			</div>
		</div>
</div>

<script>
	$(function() {
		$('.comment').each(function(){
			var $this = $(this),
			$blockies = $this.children('.blockie');
	        var icon = blockies.create({ // All options are optional
	            seed: $blockies.data('seed'), // seed used to generate icon data, default: random
	            // color: '#dfe', // to manually specify the icon color, default: random
	            size: 10, // width/height of the icon in blocks, default: 10
	            scale: 5 // width/height of each block in pixels, default: 5
	        });

	        $blockies.html(icon);	
		});
	
		
		$('.comments').infinitescroll({
			nextSelector : "#titles-nav a",
			navSelector : "#titles-nav",
			itemSelector : ".comment",
			loading : {
				finishedMsg : "<em>All comments have been loaded!</em>",
				msgText : "<em>Loading the next set of comments.</em>",
			}
		}, function(newElements){
			$(newElements).each(function(){
				var $this = $(this),
				$blockies = $this.children('.blockie');
		        var icon = blockies.create({ // All options are optional
		            seed: $blockies.data('seed'), // seed used to generate icon data, default: random
		            // color: '#dfe', // to manually specify the icon color, default: random
		            size: 10, // width/height of the icon in blocks, default: 10
		            scale: 5 // width/height of each block in pixels, default: 5
		        });

		        $blockies.html(icon);	
			});
	
		});
	});


</script>