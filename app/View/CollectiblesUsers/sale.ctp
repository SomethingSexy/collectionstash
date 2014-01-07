<?php echo $this -> Html -> script('cs.stash', array('inline' => false)); ?>
<div class="col-md-12">
	<h2><?php
	echo $stashUsername . '\'s';
	echo __(' Stash', true);
	?></h2>
	<div id="my-stashes-component" class="widget widget-tabs">
	
			<?php echo $this -> element('flash'); ?>
	
					<ul class="nav nav-tabs widget-wide">
						<?php
						echo '<li>';
						?>
						
						<?php echo '<a href="/stash/' . $stashUsername . '">' . __('Collectibles') . '</a>'; ?>
						</li>
						<?php
						echo '<li>';
						?>
						<?php echo '<a href="/wishlist/' . $stashUsername . '">' . __('Wish List') . '</a>'; ?>
						</li>
						<li class="active">
						<?php echo '<a href="/sale/' . $stashUsername . '">' . __('Sale List') . '</a>'; ?>
						</li>
						<li>
						<?php echo '<a href="/user_uploads/view/' . $stashUsername . '">' . __('Photos') . '</a>'; ?>	
						</li>
						<li><?php echo '<a href="/stashs/comments/' . $stashUsername . '">' . __('Comments') . '</a>'; ?></li>
						<li><?php echo '<a href="/stashs/history/' . $stashUsername . '">' . __('History') . '</a>'; ?></li>
					</ul>	
		<div class="widget-content">
						<?php
						if (isset($collectibles) && !empty($collectibles)) {
							
						} else {
							echo '<p class="empty">' . $stashUsername . __(' has no collectibles for sale or trade!', true) . '</p>';
						}
		?>
		</div>

	</div>
</div>
<?php echo $this -> Minify -> script('js/cs.subscribe', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.infinitescroll', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.masonry.min', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/views/view.stash.remove', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/views/view.stash.sell', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/models/model.collectible.user', array('inline' => false)); ?>

<script><?php
if (isset($reasons)) {
	echo 'var reasons = \'' . json_encode($reasons) . '\';';
}
	?></script>