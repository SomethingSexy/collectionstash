<div class="collectible image">
	<?php if (!empty($collectible['Upload'])) {?>
	<?php echo $fileUpload -> image($collectible['Upload'][0]['name'], array('width' => '100'));?>
	<div class="collectible image-fullsize hidden">
		<?php echo $fileUpload -> image($collectible['Upload'][0]['name'], array('width' => 0));?>
	</div>
	<?php } else {?>
	<img src="/img/silhouette_thumb.gif"/>
	<?php }?>
</div>