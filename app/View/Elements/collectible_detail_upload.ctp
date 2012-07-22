<div class="collectible image">
	<?php
	if (!empty($collectibleCore['Upload'])) {
		?>
		<?php echo '<a href="' . $this -> FileUpload -> image($collectibleCore['Upload'][0]['name'], array('imagePathOnly' => true,'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($collectibleCore['Upload'][0]['name'], array('imagePathOnly' => false,'width' => 400, 'height' => 400)) . '</a>';?>
		<?php } else {?><img src="/img/silhouette_thumb.png"/>
		<?php }?>
		<?php
		if (isset($showEdit) && $showEdit) {
			echo '<div class="image link">';
			if (!empty($collectibleCore['Upload'])) {
				echo '<a href="' . $editImageUrl . $collectibleCore['Collectible']['id'] . '/' . $collectibleCore['Upload'][0]['id'] . '">' . __('Edit', true) . '</a>';
			} else {
				echo '<a href="' . $editImageUrl . $collectibleCore['Collectible']['id'] . '/' . '">' . __('Edit', true) . '</a>';
			}
			echo '</div>';
		}
		?>
</div>

<script>
	$(function() {
		$('.collectible.image a').fancybox();
	}); 
</script>