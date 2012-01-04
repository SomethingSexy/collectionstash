<div class="collectible image">
	<?php
	if (!empty($collectibleCore['Upload'])) {
		?>
		<?php echo $fileUpload -> image($collectibleCore['Upload'][0]['name'], array('width' => 150, 'height' => 150));?>
		<div class="collectible image-fullsize hidden">
			<?php echo $fileUpload -> image($collectibleCore['Upload'][0]['name'], array('width' => 0));?>
		</div>
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