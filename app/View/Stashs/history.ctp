<?php echo $this -> Minify -> script('js/thirdparty/raphael', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/thirdparty/g.raphael', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/thirdparty/g.bar', array('inline' => false)); ?>





<div id="my-stashes-component" class="well">
		<div class="page-header">
			<h1><?php echo __('Search Results'); ?></h1>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="row">
			<div class="span8">
				<div class="actions stash">
					<ul class="nav nav-pills">
						<?php
						echo '<li>';
						?>
						
						<?php echo '<a href="/stash/' . $stashUsername . '">' . __('Collectibles') . '</a>'; ?>
						</li>
						<?php
						echo '<li>';
						?>
						<?php echo '<a href="/wishlist/' . $stashUsername . '">' . __('Wishlist') . '</a>'; ?>
						</li>
						<li>
						<?php echo '<a href="/user_uploads/view/' . $stashUsername . '">' . __('Photos') . '</a>'; ?>	
						</li>
						<li><?php echo '<a href="/stashs/comments/' . $stashUsername . '">' . __('Comments') . '</a>'; ?></li>
						<li class="selected"><?php echo '<a href="/stashs/history/' . $stashUsername . '">' . __('History') . '</a>'; ?></li>
					</ul>	
				</div>
				
			</div>
			<div class="span4">
				<div class="btn-group pull-right">
				    <button class="btn">All</button>
				    <button class="btn">Sold</button>
			    </div>				
			</div>
			
		</div>
			
		

			
	<div class="row">
		<div class="span12">
			<div id="holder">
		
			</div>
		</div>
		
	</div>
	<div class="row">
		<div class="span12">

		</div>
		
	</div>

</div>

<script>
	var r = Raphael('holder', 800, 250);
	var data2 = [[55, 20, 13, 32, 5, 1, 2, 10, 55, 20, 13, 32, 5, 1, 2, 10, 55, 20, 13, 32, 5, 1, 2, 10, 55, 20, 13, 32, 5, 1, 2, 10, 55, 20, 13, 32, 5, 1, 2, 10], [10, 2, 1, 5, 32, 13, 20, 55]];
	//r.barchart(10, 250, 300, 220, data2,);
	r.barchart(0, 0, 620, 260, data2, {
		stacked : true
	});

</script>