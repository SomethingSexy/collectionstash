<?php
if(!isset($adminMode)){
	$adminMode = false;
}
?>

<div class="collectible detail">
	<div class="detail title">
		<h3><?php echo __('Accessories/Features'); ?></h3>
		<?php
		if (isset($showEdit) && $showEdit) {
			echo '<div class="actions icon">';
			echo '<ul>';
			echo '<li>';
			if($adminMode){
				echo '<a href="/attributes_collectibles_edits/edit/' . $collectibleCore['Collectible']['id'] . '/true' . '"><img src="/img/icon/pencil.png"/></a>';
			} else {
				echo '<a href="/attributes_collectibles_edits/edit/' . $collectibleCore['Collectible']['id'] . '/' . '"><img src="/img/icon/pencil.png"/></a>';
			}
			echo '</li>';
			echo '</ul>';
			echo '</div>';
		}
		?>
	</div>
	<?php
	$lastKey = 0;
	$attributeEmpty = empty($collectibleCore['AttributesCollectible']);
	if ($attributeEmpty) {
		echo '<div class="attributes-list empty">';
		echo '<ul>';
		echo '<li>No Accessories/Features have been added</li>';
		echo '</ul>';
		echo '</div>';
	} else {
		$outputAttribtes = '';
		$added = false;
		foreach ($collectibleCore['AttributesCollectible'] as $key => $attribute) {
			$outputAttribtes .= '<li>' . '<span class="attribute-name">' . $attribute['Attribute']['name'] . '</span>' . '<span class="attribute-description">' . $attribute['description'] . '</span>' . '</li>';
			$added = true;
		}

		if ($added) {
			echo '<div class="attributes-list">';
			echo '<ul>';
			echo '<li class="title">';
			echo '<span class="attribute-name">' . __('Part', true) . '</span>';
			echo '<span class="attribute-description">' . __('Description', true) . '</span>';
			echo '</li>';
			echo $outputAttribtes;
			echo '</ul>';
			echo '</div>';
		} else {
			echo '<div class="attributes-list empty">';
			echo '<ul>';
			echo '<li>No Accessories/Features have been added</li>';
			echo '</ul>';
			echo '</div>';
		}
	}
	?>
</div>