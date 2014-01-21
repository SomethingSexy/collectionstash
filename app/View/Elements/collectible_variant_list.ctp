<div class="panel panel-default">
	<div class="panel-heading">	
 		<h3 class="panel-title"><?php echo __('Variants'); ?></h3>
	</div>
	<div class="panel-body">
<?php if (!empty($variants)) { ?>
	<ul class="list-unstyled">
	<?php  
	foreach ($variants as $variant):
	?>
		
			<li class="col-md-3">
				<?php
				if (!empty($variant['CollectiblesUpload'])) {
					foreach ($variant['CollectiblesUpload'] as $key => $upload) {
						if ($upload['primary']) {
							$this -> FileUpload -> reset();
							echo '<a  class="thumbnail col-md-12" href="/collectibles/view/' . $variant['Collectible']['id'] . '">';
							echo $this -> FileUpload -> image($upload['Upload']['name'], array('escape' => false));
							echo '</a>';
							break;
						}
					}
					$this -> FileUpload -> reset();
				} else {
					echo '<a class="thumbnail" href="/collectibles/view/' . $variant['Collectible']['id'] . '"><img alt="" src="/img/no-photo.png"></a>';
				}
			?>
			</li>
	<?php endforeach; ?>      
	</ul>
<?php } else { ?>

	<p><?php echo __('This collectible has no variants.'); ?></p>	
	
<?php	  } ?>
	</div>
</div>